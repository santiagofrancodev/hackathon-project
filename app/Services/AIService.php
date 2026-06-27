<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIService
{
    private string $apiKey;

    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
    }

    public function explicarPregunta(string $preguntaTexto): string
    {
        $prompt = "Eres un experto en la Ley 1581 de 2012 de Colombia (protección de datos personales). 
        Explica en lenguaje simple y amigable, en máximo 3 oraciones, qué significa esta pregunta 
        de autodiagnóstico para una pyme colombiana: \"{$preguntaTexto}\"";

        return $this->call($prompt);
    }

    public function generarRecomendacionIA(string $preguntaTexto, string $empresa): string
    {
        $prompt = "Eres un consultor de protección de datos colombiano. 
        La empresa '{$empresa}' respondió negativamente a: '{$preguntaTexto}'. 
        Da una recomendación concreta, práctica y accionable en máximo 2 oraciones.";

        return $this->call($prompt);
    }

    public function interpretarResultado(int $score, string $empresa): string
    {
        $prompt = "Eres un experto en Ley 1581. La empresa '{$empresa}' obtuvo {$score}%
        de cumplimiento en su autodiagnóstico de protección de datos.
        Interpreta este resultado en 2-3 oraciones: qué significa, cuáles son los riesgos principales
        y un mensaje motivador para mejorar.";

        return $this->call($prompt);
    }

    public function generarInformeEjecutivo(
        string $empresa,
        string $sector,
        string $tamano,
        int $score,
        string $bloques,
        string $gapsDetallados,
        string $recomendaciones,
    ): string {
        $nivel = $score >= 80 ? 'ALTO' : ($score >= 60 ? 'MODERADO' : ($score >= 40 ? 'BAJO' : 'CRÍTICO'));

        $prompt = <<<PROMPT
Eres un consultor senior experto en la Ley 1581 de 2012 de Colombia. Redacta un INFORME EJECUTIVO DE DIAGNÓSTICO completo y profesional en español neutro.

## DATOS DE LA EMPRESA
- Nombre: {$empresa}
- Sector: {$sector}
- Tamaño: {$tamano}

## RESULTADO GLOBAL
- Puntaje total: {$score}% — Nivel {$nivel}

## DESGLOSE POR BLOQUE
{$bloques}

## BRECHAS IDENTIFICADAS (areas de mejora)
{$gapsDetallados}

## RECOMENDACIONES POR PRIORIDAD
{$recomendaciones}

---

Genera un INFORME EJECUTIVO estructurado EXACTAMENTE con estas secciones (usa los titulos textuales sin numeracion):

=== DIAGNÓSTICO GENERAL ===
Analiza el puntaje total ({$score}%) en el contexto del sector {$sector}. Explica que significa este nivel de cumplimiento para una empresa de este tamaño y rubro. Menciona los bloques mejor y peor evaluados.

=== RIESGOS LEGALES IDENTIFICADOS ===
Lista y explica los principales riesgos legales y sanciones economicas potenciales segun la Ley 1581 (multas SIC hasta 2.000 SMMLV). Relaciona cada riesgo con las brechas especificas encontradas.

=== PRIORIDADES DE ACCIÓN ===
Ordena las acciones recomendadas por criticidad (ALTA/MEDIA/BAJA). Para cada prioridad, incluye: el area problematica, la accion concreta y el impacto esperado.

=== PRÓXIMOS PASOS ===
Hoja de ruta recomendada: acciones a 30, 60 y 90 dias. Un mensaje final motivador sobre la importancia de la proteccion de datos.
PROMPT;

        return $this->call($prompt, 1200);
    }

    private function call(string $prompt, int $maxTokens = 300): string
    {
        if (empty($this->apiKey)) {
            return 'Configure la clave de API en el archivo .env para usar esta función.';
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => 'Eres un asistente experto en la Ley 1581 de Colombia. Responde siempre en español neutro, claro y conciso.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => $maxTokens,
            'temperature' => 0.7,
        ]);

        if ($response->failed()) {
            return 'No se pudo generar la respuesta en este momento.';
        }

        return $response->json('choices.0.message.content', 'No se pudo generar la respuesta.');
    }
}
