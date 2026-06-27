<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Category;
use App\Models\Company;
use App\Models\Question;
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
            'answers' => 'sometimes|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'nullable|boolean',
            'answers.*.notes' => 'nullable|string|max:1000',
        ]);

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

        return redirect()->route('diagnostic.results', $assessment)
            ->with('success', 'Autodiagnóstico completado. Este es tu resultado.');
    }

    public function results(Assessment $assessment)
    {
        $this->authorizeAccess($assessment);

        $assessment->load('answers.question');

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

    private function authorizeAccess(Assessment $assessment): void
    {
        if ($assessment->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para acceder a esta evaluación.');
        }
    }
}
