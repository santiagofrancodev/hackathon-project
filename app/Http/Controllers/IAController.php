<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
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

        // Check if user has a pro company for AI features
        $userCompanies = auth()->user()->companies()->get();
        $hasPro = $userCompanies->contains(fn ($c) => $c->isPro());

        if (! $hasPro && ! config('cumplia.demo_mode')) {
            return response()->json([
                'explicacion' => 'Actualice a plan Pro para acceder a las explicaciones con Inteligencia Artificial.',
            ]);
        }

        $explicacion = $ai->explicarPregunta($question->question_text);

        return response()->json(['explicacion' => $explicacion]);
    }

    public function interpretarResultado(Request $request, AIService $ai)
    {
        $validated = $request->validate([
            'assessment_id' => 'required|exists:assessments,id',
        ]);

        $assessment = Assessment::with('company')->findOrFail($validated['assessment_id']);

        $this->authorizeAccess($assessment);

        if ($assessment->company->isFree()) {
            return response()->json([
                'interpretacion' => 'Actualice a plan Pro para acceder a la interpretación de resultados con Inteligencia Artificial.',
            ]);
        }

        $interpretacion = $ai->interpretarResultado(
            (int) $assessment->score,
            $assessment->company->name
        );

        return response()->json(['interpretacion' => $interpretacion]);
    }

    private function authorizeAccess(Assessment $assessment): void
    {
        if ($assessment->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
