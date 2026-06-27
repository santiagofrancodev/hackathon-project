<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Category;
use App\Models\Question;
use App\Services\AIService;
use Illuminate\Http\Request;

class IAController extends Controller
{
    public function explicarPregunta(Request $request, AIService $ai)
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
        ]);

        $question = Question::findOrFail($validated['question_id']);

        $userCompanies = auth()->user()->companies()->get();
        $hasPro = $userCompanies->contains(fn ($c) => $c->isPro());

        if (! $hasPro) {
            return response()->json([
                'explicacion' => 'Actualice a plan Pro para acceder a las explicaciones con Inteligencia Artificial.',
            ]);
        }

        $explicacion = $ai->explicarPregunta($question->question_text);

        return response()->json(['explicacion' => $explicacion]);
    }

    public function generarInforme(Request $request, AIService $ai)
    {
        $validated = $request->validate([
            'assessment_id' => 'required|exists:assessments,id',
        ]);

        $assessment = Assessment::with(['company', 'answers.question', 'recommendations'])
            ->findOrFail($validated['assessment_id']);

        $this->authorizeAccess($assessment);

        if ($assessment->company->isFree()) {
            return response()->json([
                'summary' => 'Actualice a plan Pro para acceder al informe ejecutivo con Inteligencia Artificial.',
            ]);
        }

        // Return cached summary if it already exists
        if ($assessment->ai_summary) {
            return response()->json(['summary' => $assessment->ai_summary]);
        }

        $summary = $this->buildAndStoreSummary($assessment, $ai);

        return response()->json(['summary' => $summary]);
    }

    private function buildAndStoreSummary(Assessment $assessment, AIService $ai): string
    {
        $company = $assessment->company;
        $tamano = ['small' => 'Pequeña', 'medium' => 'Mediana', 'large' => 'Grande'][$company->size] ?? 'No especificado';
        $sector = $company->sector ?? 'No especificado';

        $categories = Category::with(['questions' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $answersByQuestion = $assessment->answers->keyBy('question_id');
        $bloques = '';
        foreach ($categories as $cat) {
            $earned = 0;
            $total = 0;
            foreach ($cat->questions as $q) {
                if ($q->is_complementary || $q->weight === 0) {
                    continue;
                }
                $total += $q->weight;
                $ans = $answersByQuestion->get($q->id);
                if ($ans && $ans->answer) {
                    $earned += $q->weight;
                }
            }
            $pct = $total > 0 ? round(($earned / $total) * $cat->max_percentage) : 0;
            $bloques .= "- {$cat->name}: {$pct}% / {$cat->max_percentage}%\n";
        }

        $gapsDetallados = '';
        foreach ($assessment->answers as $answer) {
            if ($answer->answer || ! $answer->question) {
                continue;
            }
            $q = $answer->question;
            if ($q->is_complementary || $q->weight === 0) {
                continue;
            }
            $gapsDetallados .= "- [{$q->weight}%] {$q->question_text}\n";
        }

        $recomendaciones = '';
        foreach (['high' => 'ALTA', 'medium' => 'MEDIA', 'low' => 'BAJA'] as $pri => $label) {
            $items = $assessment->recommendations->where('priority', $pri);
            if ($items->isEmpty()) {
                continue;
            }
            $recomendaciones .= "=== {$label} ===\n";
            foreach ($items as $rec) {
                $origen = $rec->origin === 'ai' ? 'IA' : 'Regla';
                $recomendaciones .= "- [{$origen}] {$rec->text}\n";
            }
        }

        $summary = $ai->generarInformeEjecutivo(
            $company->name,
            $sector,
            $tamano,
            (int) $assessment->score,
            $bloques,
            $gapsDetallados,
            $recomendaciones,
        );

        $assessment->update(['ai_summary' => $summary]);

        return $summary;
    }

    private function authorizeAccess(Assessment $assessment): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isAuditor() && $user->auditedCompanies()->where('company_id', $assessment->company_id)->exists()) {
            return;
        }

        if ($assessment->user_id !== $user->id) {
            abort(403);
        }
    }
}
