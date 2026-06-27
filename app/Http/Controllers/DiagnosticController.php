<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Category;
use App\Models\Company;
use App\Models\Question;
use App\Models\Recommendation;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiagnosticController extends Controller
{
    public function index()
    {
        $categories = Category::with('questions')->orderBy('sort_order')->get();
        $companies = Company::where('user_id', Auth::id())->get();

        return view('diagnostic.index', compact('categories', 'companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        // Ensure the company belongs to the user
        $company = Company::where('id', $validated['company_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $assessment = Assessment::create([
            'company_id' => $company->id,
            'user_id' => Auth::id(),
            'status' => 'in_progress',
        ]);

        return redirect()->route('diagnostic.show', $assessment)
            ->with('success', 'Autodiagnóstico iniciado correctamente.');
    }

    public function show(Assessment $assessment)
    {
        $this->authorizeAccess($assessment);

        $assessment->load(['answers', 'company']);

        $categories = Category::with(['questions' => function ($query) {
            $query->orderBy('sort_order');
        }])->orderBy('sort_order')->get();

        $answers = $assessment->answers->keyBy('question_id');

        return view('diagnostic.show', compact('assessment', 'categories', 'answers'));
    }

    public function submit(Request $request, Assessment $assessment)
    {
        $this->authorizeAccess($assessment);

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'nullable|boolean',
            'answers.*.notes' => 'nullable|string|max:1000',
        ]);

        // Ensure at least one question was actually answered
        $hasAnyAnswer = collect($validated['answers'])->contains(fn ($a) => isset($a['answer']) && $a['answer'] !== null);
        if (! $hasAnyAnswer) {
            return back()->withErrors(['answers' => 'Debe responder al menos una pregunta antes de enviar el cuestionario.'])->withInput();
        }

        $submittedAnswers = $validated['answers'] ?? [];

        // Get all answerable questions (non-complementary, weight > 0)
        $answerableQuestions = Question::where('is_complementary', false)
            ->where('weight', '>', 0)
            ->pluck('id')
            ->toArray();

        $submittedQuestionIds = array_column($submittedAnswers, 'question_id');

        // Add missing questions as "No" answers
        foreach ($answerableQuestions as $questionId) {
            if (! in_array($questionId, $submittedQuestionIds)) {
                $submittedAnswers[] = [
                    'question_id' => $questionId,
                    'answer' => false,
                    'notes' => null,
                ];
            }
        }

        foreach ($submittedAnswers as $answerData) {
            $assessment->answers()->updateOrCreate(
                ['question_id' => $answerData['question_id']],
                [
                    'answer' => $answerData['answer'] ?? false,
                    'notes' => $answerData['notes'] ?? null,
                ]
            );
        }

        // Refresh answers relationship for scoring
        $assessment->load('answers');

        // Calculate score
        $score = $this->calculateScore($assessment);

        $assessment->update([
            'status' => 'completed',
            'score' => $score,
        ]);

        // Generate rule-based recommendations for gaps
        $this->generateRecommendations($assessment);

        // Generate AI summary
        try {
            $this->generateAiSummary($assessment);
        } catch (\Exception $e) {
            // AI summary is optional — don't block submission
        }

        return redirect()->route('diagnostic.results', $assessment)
            ->with('success', 'Autodiagnóstico completado. Este es tu resultado.');
    }

    public function results(Assessment $assessment)
    {
        $this->authorizeAccess($assessment);

        $assessment->load(['answers.question', 'recommendations']);

        $categories = Category::with(['questions' => function ($query) {
            $query->orderBy('sort_order');
        }])->orderBy('sort_order')->get();

        $answers = $assessment->answers->keyBy('question_id');

        // Per-category breakdown
        $categoryResults = [];
        foreach ($categories as $category) {
            $earned = 0;
            $totalWeight = 0;
            foreach ($category->questions as $question) {
                if ($question->is_complementary || $question->weight === 0) {
                    continue;
                }
                $totalWeight += $question->weight;
                $answer = $answers->get($question->id);
                if ($answer && $answer->answer) {
                    $earned += $question->weight;
                }
            }
            $categoryResults[$category->id] = [
                'name' => $category->name,
                'max_percentage' => $category->max_percentage,
                'earned_percentage' => $totalWeight > 0
                    ? round(($earned / $totalWeight) * $category->max_percentage)
                    : 0,
                'earned_weight' => $earned,
                'total_weight' => $totalWeight,
            ];
        }

        // Identify gaps (questions answered negatively)
        $gaps = [];
        foreach ($categories as $category) {
            foreach ($category->questions as $question) {
                if ($question->is_complementary) {
                    continue;
                }
                $answer = $answers->get($question->id);
                if (! $answer || ! $answer->answer) {
                    $gaps[] = [
                        'category' => $category->name,
                        'question' => $question->question_text,
                        'help_text' => $question->help_text,
                    ];
                }
            }
        }

        return view('diagnostic.results', compact(
            'assessment', 'categories', 'answers', 'categoryResults', 'gaps'
        ));
    }

    private function calculateScore(Assessment $assessment): int
    {
        $totalEarnedPercentage = 0;
        $categories = Category::with('questions')->get();

        foreach ($categories as $category) {
            $earned = 0;
            $totalWeight = 0;
            foreach ($category->questions as $question) {
                if ($question->is_complementary || $question->weight === 0) {
                    continue;
                }
                $totalWeight += $question->weight;
                $answer = $assessment->answers->where('question_id', $question->id)->first();
                if ($answer && $answer->answer) {
                    $earned += $question->weight;
                }
            }
            if ($totalWeight > 0) {
                $totalEarnedPercentage += ($earned / $totalWeight) * $category->max_percentage;
            }
        }

        return (int) round(min($totalEarnedPercentage, 100));
    }

    private function generateAiSummary(Assessment $assessment): void
    {
        $assessment->load('company', 'recommendations');

        // Free plan users don't get AI summary
        if ($assessment->company->isFree()) {
            return;
        }

        $brechas = $assessment->recommendations
            ->where('origin', 'rule')
            ->pluck('text')
            ->implode('; ');

        $ai = app(AIService::class);
        $sector = $assessment->company->sector ?? 'No especificado';

        $summary = $ai->generarInformeEjecutivo(
            $assessment->company->name,
            $sector,
            (int) $assessment->score,
            $brechas
        );

        $assessment->update(['ai_summary' => $summary]);
    }

    private function generateRecommendations(Assessment $assessment): void
    {
        $assessment->load('company');
        $answers = $assessment->answers()->with('question')->get();
        $recommendationTexts = config('recommendations.by_question', []);
        $ai = app(AIService::class);

        foreach ($answers as $answer) {
            $question = $answer->question;

            // Only generate for negative answers on countable questions
            if ($answer->answer || $question->is_complementary || $question->weight === 0) {
                continue;
            }

            // Determine priority based on weight
            $priority = match (true) {
                $question->weight >= 12 => 'high',
                $question->weight >= 8 => 'medium',
                default => 'low',
            };

            // For high-priority gaps on pro plans, use AI
            if ($priority === 'high' && $assessment->company->isPro()) {
                try {
                    $text = $ai->generarRecomendacionIA(
                        $question->question_text,
                        $assessment->company->name
                    );
                    Recommendation::create([
                        'assessment_id' => $assessment->id,
                        'question_id' => $question->id,
                        'text' => $text,
                        'priority' => 'high',
                        'origin' => 'ai',
                    ]);

                    continue;
                } catch (\Exception $e) {
                    // Fall through to rule-based
                }
            }

            // Get recommendation text from config, or generate a default
            $text = $recommendationTexts[$question->id] ?? sprintf(
                'Revise e implemente medidas para abordar: %s',
                $question->question_text
            );

            Recommendation::create([
                'assessment_id' => $assessment->id,
                'question_id' => $question->id,
                'text' => $text,
                'priority' => $priority,
                'origin' => 'rule',
            ]);
        }
    }

    private function authorizeAccess(Assessment $assessment): void
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isAuditor()) {
            $hasAccess = $user->auditedCompanies()
                ->where('company_id', $assessment->company_id)
                ->exists();

            if ($hasAccess) {
                return;
            }
        }

        if ($assessment->user_id !== $user->id) {
            abort(403, 'No tienes permiso para acceder a esta evaluación.');
        }
    }
}
