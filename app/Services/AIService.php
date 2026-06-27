<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private string $apiKey;

    private string $apiKeyFallback;

    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', '');
        $this->apiKeyFallback = config('services.openai.api_key_fallback', '');
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
Eres un consultor senior experto en la Ley 1581 de 2012 de Colombia.
Tu tarea es redactar un INFORME DE DIAGNÓSTICO en español neutro, con un tono
**cálido, cercano y motivador**, como si estuvieras hablando directamente con
el dueño de la PYME. Nada de jerga técnica innecesaria.

Usá el siguiente formato exacto. Cada sección va con ## y un texto amigable:

## Diagnóstico general

(Empezá con un tono cercano, tipo "Hola! Veamos cómo le fue a tu empresa..."
o similar. Explicá el score de forma clara. Destacá qué bloque le fue mejor
y cuál peor. Constructivo, no alarmista.)

## Riesgos legales que deberías conocer

(Explicá los riesgos de forma clara pero sin asustar. Mencioná las multas
de hasta 2.000 SMMLV como algo importante de conocer. Relacioná cada riesgo
con las brechas concretas de la empresa.)

## Tu plan de acción prioritario

(Ordená las acciones por prioridad. Usá este formato:

**Prioridad ALTA**
• *Área*: [nombre] — *Acción*: [qué hacer] — *Impacto*: [qué mejora]

**Prioridad MEDIA**
• ...)

## Próximos pasos: tu hoja de ruta

(Recomendación amigable de qué hacer en 30, 60 y 90 días. Cerra con un
mensaje motivador: "La protección de datos no es solo una obligación legal,
es una forma de demostrarle a tus clientes que su información está segura".)

--- DATOS PARA EL INFORME ---
Empresa: {$empresa} | Sector: {$sector} | Tamaño: {$tamano}
Puntaje: {$score}% (Nivel {$nivel})

Desglose por bloque:
{$bloques}

Brechas encontradas:
{$gapsDetallados}

Recomendaciones:
{$recomendaciones}

IMPORTANTE: NO uses === ni símbolos raros. Usá ## para los títulos.
Tono cálido, cercano y constructivo, como un consultor amigo.
PROMPT;

        return $this->call($prompt, 1500);
    }

    private function call(string $prompt, int $maxTokens = 300): string
    {
        $keys = array_filter([$this->apiKey, $this->apiKeyFallback]);

        if (empty($keys)) {
            return 'Configure la clave de API en el archivo .env para usar esta función.';
        }

        foreach ($keys as $key) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$key,
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

                if ($response->status() === 401) {
                    Log::warning('OpenAI API error: 401 — '.$response->body());

                    continue;
                }

                if ($response->failed()) {
                    Log::warning('OpenAI API error: '.$response->status().' — '.$response->body());

                    return 'No se pudo generar la respuesta en este momento. (Error: '.$response->status().')';
                }

                return $response->json('choices.0.message.content', 'No se pudo generar la respuesta.');
            } catch (RequestException $e) {
                Log::error('OpenAI API exception: '.$e->getMessage());

                return 'No se pudo conectar con el servicio de IA en este momento.';
            }
        }

        return 'No se pudo generar la respuesta en este momento. (Error: 401)';
    }
}
