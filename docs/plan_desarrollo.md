# CumplIA — Plan de Desarrollo por Fases

> **Para quién es este documento:** Es la guía de implementación para el modelo ejecutor.
> El código base ya existe en Laravel 11 + Blade + SQLite. Este plan parte de ese estado real —
> no del contexto técnico de `/docs/contexto_tecnico.md`, que describe una arquitectura SPA/React
> que nunca se construyó. **Ignorar ese stack; trabajar exclusivamente con el código existente.**

---

## Estado actual verificado (línea de partida)

| Componente | Estado |
|---|---|
| Framework | Laravel 11 + Breeze (email/password) + Alpine.js + Tailwind |
| BD | SQLite (default en `config/database.php`) — no cambiar |
| Vistas | Blade (todas en `resources/views/`) |
| Rutas web | `routes/web.php` — flujo completo: empresa → diagnóstico → resultados |
| Rutas API | `routes/api.php` — API Sanctum a medio construir; **ignorar, no tocar** |
| Modelos | `User`, `Company`, `Question`, `Category`, `Assessment`, `Answer` |
| Migraciones | 7 tablas completas y correctas |
| Seeder | `DiagnosticSeeder` — 11 preguntas con pesos exactos según el reto |
| Scoring | `DiagnosticController::calculateScore()` — matemática correcta |
| Gauge | SVG puro en `results.blade.php` — funciona |

### Bug conocido (CRÍTICO — arreglar en Fase 0)

En `resources/views/diagnostic/results.blade.php`, líneas ~69-70, hay variables PHP
con backslash incorrecto dentro de `@php`:

```blade
@php \$barWidth = ...; @endphp
<div ... data-width="{{ \$barWidth }}">
```

**Arreglo**: quitar todos los `\$` → `$barWidth`, `$pct`, `$total` (sin backslash).

---

## Principios de implementación

1. **Stack fijo**: Laravel 11 + Blade + Alpine.js + Tailwind. Sin React, sin Vue, sin APIs REST nuevas.
2. **BD portable**: SQLite ahora, pero NUNCA usar pragmas SQLite-específicos ni `INTEGER PRIMARY KEY AUTOINCREMENT`. Usar siempre `$table->id()` y los tipos de Eloquent — portabilidad garantizada a MySQL/Postgres cambiando 4 líneas de `.env`.
3. **Test mínimo obligatorio**: cada fase debe poder recorrerse manualmente end-to-end sin errores 500.
4. **Sin romper lo que funciona**: no refactorizar el scoring, no renombrar modelos, no cambiar migraciones ya corridas.
5. **Credenciales en `.env` siempre**: jamás hardcodear API keys. Usar `config('services.gemini.api_key')`.

---

## Fase 0 — Blindar el MVP / Nivel Básico · Estimado: 1h

**Objetivo**: tener el flujo completo funcionando sin errores. Demo de Nivel 1 lista.

### Tareas

#### 0.1 — Arreglar bug `\$` en results.blade.php

Archivo: `resources/views/diagnostic/results.blade.php`

Encontrar el bloque `@php` que tiene `\$barWidth`, `\$pct`, `\$total` y reemplazar por
`$barWidth`, `$pct`, `$total` (sin backslash). Verificar que las barras de progreso
se rendericen correctamente.

#### 0.2 — Verificar lógica condicional de preguntas en diagnostic/show.blade.php

Archivo: `resources/views/diagnostic/show.blade.php`

Las preguntas padre-hijo tienen `parent_question_id` en la BD. La vista DEBE:
- Mostrar Q2–Q5 (hijas de Q1) **solo si Q1 = Sí** (con Alpine.js `x-show`)
- Mostrar Q11 (hija de Q10) **solo si Q10 = Sí**
- Si la vista no lo hace, implementarlo con Alpine.js

Patrón Alpine sugerido para cada pregunta padre:
```html
<div x-data="{ respuesta: false }">
    <!-- Toggle Q1 -->
    <input type="checkbox" x-model="respuesta" ...>
    
    <!-- Hijas de Q1, visibles solo si respuesta = true -->
    <div x-show="respuesta" x-transition>
        <!-- Q2, Q3, Q4, Q5 -->
    </div>
</div>
```

#### 0.3 — Verificar/crear landing page

Archivo: `resources/views/welcome.blade.php`

La landing debe tener:
- Nombre "CumplIA" visible en el hero
- Tagline: *"De la incertidumbre legal a un plan de acción priorizado, en minutos"*
- Botón CTA que apunta a `/register`
- Sección de 3 features: Diagnóstico Inteligente, Cumplimiento Ley 1581, Recomendaciones
- Footer con nombre del producto

Si ya existe con este contenido, no tocar.

#### 0.4 — Verificar dashboard

Archivo: `resources/views/dashboard.blade.php`

El dashboard debe mostrar:
- Lista de evaluaciones pasadas del usuario (empresa, fecha, score %)
- Botón "Nueva evaluación"
- Si no tiene ninguna, mensaje de estado vacío con CTA

#### 0.5 — Ejecutar y verificar

```bash
php artisan migrate:fresh --seed
php artisan serve
```

Recorrer manualmente: registro → crear empresa → iniciar diagnóstico → responder → ver resultados.

---

## Fase 1 — Nivel Intermedio · Estimado: 3h

**Objetivo**: completar el Nivel 2 del reto. Agregar OAuth, recomendaciones por reglas e histórico.

### Tareas

#### 1.1 — Tabla y modelo `Recommendation`

Crear migración:

```bash
php artisan make:migration create_recommendations_table
```

Esquema:
```php
Schema::create('recommendations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
    $table->foreignId('question_id')->nullable()->constrained()->nullOnDelete();
    $table->text('text');
    $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
    $table->enum('origin', ['rule', 'ai'])->default('rule');
    $table->timestamps();
});
```

Crear modelo:
```bash
php artisan make:model Recommendation
```

Relaciones: `belongsTo(Assessment)`, `belongsTo(Question)`.
Agregar `hasMany(Recommendation)` en `Assessment`.

#### 1.2 — Generador de recomendaciones por reglas

En `DiagnosticController::submit()`, después de `calculateScore()`, generar recomendaciones
por regla para cada brecha (pregunta con respuesta negativa y peso > 0):

Lógica de prioridad según peso:
- peso ≥ 12 → `'high'`
- peso ≥ 8 → `'medium'`
- peso < 8 → `'low'`

Textos de recomendación pre-definidos por `question_id` (hardcodear en el controlador
o en un array de configuración `config/recommendations.php`):

| Pregunta | Recomendación |
|---|---|
| Q2 (política documentada) | Redacte y publique su política de tratamiento de datos en el sitio web corporativo y en todos los puntos de atención al titular. |
| Q3 (finalidades) | Incluya en su política una sección explícita que detalle cada finalidad del tratamiento: comercial, operativa, estadística, etc. |
| Q4 (derechos) | Incorpore en su política los derechos ARCO (Acceso, Rectificación, Cancelación, Oposición) que la Ley 1581 reconoce a los titulares. |
| Q5 (ejercer derechos) | Defina canales formales (correo, formulario web) y plazos de respuesta para que los titulares puedan ejercer sus derechos. |
| Q6 (PIA) | Implemente evaluaciones de impacto de privacidad (PIA) antes de lanzar nuevos proyectos que traten datos personales. |
| Q7 (minimización) | Audite los datos que recolecta y elimine los que no sean estrictamente necesarios para la finalidad declarada. |
| Q8 (mínimo por defecto) | Reconfigure sus formularios y sistemas para que, por defecto, solo soliciten los campos obligatorios. |
| Q9 (riesgos) | Establezca una matriz de riesgos de privacidad con periodicidad de revisión anual como mínimo. |
| Q10 (oficial DPO) | Designe un Oficial de Protección de Datos (DPO) responsable del cumplimiento de la Ley 1581. |

#### 1.3 — Mostrar recomendaciones en results.blade.php

Después de la sección "Brechas Identificadas", agregar sección "Plan de Acción":

```blade
<!-- Plan de Acción -->
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Plan de Acción Recomendado</h3>
        @foreach($assessment->recommendations->sortBy(fn($r) => match($r->priority) { 'high' => 0, 'medium' => 1, 'low' => 2 }) as $rec)
            <div class="flex items-start gap-3 mb-3 p-3 border rounded-lg">
                <span class="px-2 py-1 text-xs font-bold rounded
                    {{ $rec->priority === 'high' ? 'bg-red-100 text-red-700' :
                       ($rec->priority === 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                    {{ $rec->priority === 'high' ? 'Alta' : ($rec->priority === 'medium' ? 'Media' : 'Baja') }}
                </span>
                <p class="text-sm text-gray-700">{{ $rec->text }}</p>
            </div>
        @endforeach
    </div>
</div>
```

#### 1.4 — OAuth con Google (Socialite)

Instalar:
```bash
composer require laravel/socialite
```

Agregar en `config/services.php`:
```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
],
```

Agregar en `.env.example`:
```
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

Crear controlador:
```bash
php artisan make:controller Auth/SocialiteController
```

Métodos:
- `redirectToGoogle()` → `Socialite::driver('google')->redirect()`
- `handleGoogleCallback()` → buscar/crear usuario, login, redirect a dashboard

En `User`, hacer `password` nullable y agregar fillable: `provider`, `provider_id`.
Crear migración para agregar esas columnas al users table.

Rutas en `routes/web.php` (fuera de middleware `auth`):
```php
Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);
```

En `resources/views/auth/login.blade.php`, agregar botón:
```blade
<a href="{{ route('auth.google') }}" class="...">
    Continuar con Google
</a>
```

#### 1.5 — Histórico en dashboard

En `DashboardController::index()`, cargar las evaluaciones completadas del usuario con empresa:

```php
$assessments = Assessment::with('company')
    ->where('user_id', Auth::id())
    ->where('status', 'completed')
    ->orderByDesc('created_at')
    ->get();
```

En `dashboard.blade.php`, mostrar tabla con: empresa, fecha, score (con badge de color).

---

## Fase 2 — Nivel Avanzado · Estimado: 4h

**Objetivo**: integrar IA, roles, multiempresa y PDF. Criterios de evaluación máximos.

### Tareas

#### 2.1 — Servicio de IA (Gemini Flash)

Crear `app/Services/GeminiService.php`:

```php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-2.0-flash');
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

    private function call(string $prompt): string
    {
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [['parts' => [['text' => $prompt]]]],
            ]);

        return $response->json('candidates.0.content.parts.0.text', 'No se pudo generar la respuesta.');
    }
}
```

Registrar en `config/services.php`:
```php
'gemini' => [
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
],
```

Agregar en `.env.example`:
```
GEMINI_API_KEY=
GEMINI_MODEL=gemini-2.0-flash
```

#### 2.2 — Endpoint AJAX para explicar pregunta

Ruta en `routes/web.php` (auth requerido):
```php
Route::post('/ia/explicar-pregunta', [IAController::class, 'explicarPregunta'])->name('ia.explicar');
```

Controlador `app/Http/Controllers/IAController.php`:
```php
public function explicarPregunta(Request $request)
{
    $request->validate(['question_id' => 'required|exists:questions,id']);
    $question = Question::findOrFail($request->question_id);
    $explicacion = app(GeminiService::class)->explicarPregunta($question->question_text);
    return response()->json(['explicacion' => $explicacion]);
}
```

En `diagnostic/show.blade.php`, agregar botón "?" junto a cada pregunta que llame al endpoint
via `fetch` y muestre el resultado en un modal Alpine.js.

#### 2.3 — Recomendaciones con IA al finalizar diagnóstico

En `DiagnosticController::submit()`, después de generar las recomendaciones por regla,
**reemplazar el texto de las de prioridad `high` con respuesta de Gemini**:

```php
foreach ($highPriorityGaps as $question) {
    $iaText = app(GeminiService::class)->generarRecomendacionIA(
        $question->question_text,
        $assessment->company->name
    );
    Recommendation::create([
        'assessment_id' => $assessment->id,
        'question_id' => $question->id,
        'text' => $iaText,
        'priority' => 'high',
        'origin' => 'ai',
    ]);
}
```

En `results.blade.php`, agregar badge de origen (`IA` en índigo, `Regla` en gris).

Agregar también la interpretación del resultado al tope de la vista:
```php
// En DiagnosticController::results()
$interpretacion = app(GeminiService::class)->interpretarResultado(
    $assessment->score,
    $assessment->company->name
);
```

#### 2.4 — Roles de usuario (4 roles)

> **Decisión de arquitectura (CumplIA).** El reto pide 3 roles —admin, evaluator
> (la "empresa evaluada", quien se autodiagnostica) y auditor—. Agregamos un **4º rol
> `user`** para el modelo self-service B2C freemium, que es el diferenciador de monetización
> del pitch. `user` y `evaluator` no son redundantes: se distinguen por **origen, plan y
> visibilidad para auditores**.

**Definición de los 4 roles:**

| Rol | Quién es | Cómo se crea | IA | Visible a auditores |
|---|---|---|---|---|
| `user` | Cliente self-service B2C que se autodiagnostica | Auto-registro en `/register` | ❌ `free` (paga → desbloquea IA) | ❌ Nunca |
| `evaluator` | La "empresa evaluada" en contexto gestionado/B2B | Lo crea un admin | ✅ `pro` | ✅ Si tiene auditor asignado |
| `auditor` | Consultor/revisor externo especializado en Ley 1581 | Lo crea un admin | — (solo lectura) | — |
| `admin` | Operador de la plataforma | Seeder | ✅ | — |

**Función del auditor en la plataforma.** Es el tercero profesional (DPO tercerizado,
consultor o abogado de protección de datos) que **revisa y valida** los diagnósticos desde
afuera —no los crea—. La diferencia de fondo con el evaluator es escritura vs lectura: el
evaluator ESCRIBE el cuestionario, el auditor solo LEE las evaluaciones de las empresas que
un admin le asignó vía el pivot `auditor_company`. Su visibilidad depende de esa asignación
manual; por eso un `user` self-service nunca es visible para un auditor (nadie se lo asigna).
Capacidades: dashboard de solo-lectura de empresas asignadas, ver detalle de cada diagnóstico,
descargar PDF y *(opcional, suma como innovación)* dejar un **concepto/observación** sobre una
evaluación. NO puede crear empresas, responder cuestionarios ni editar diagnósticos.

**Estado actual:** ✅ migración `add_role_to_users_table` corrida (columna `role` `string`
default `evaluator`), ✅ `AssessmentPolicy` con 3 roles, ✅ `AppServiceProvider` registra
policy + gates, ✅ pivot `auditor_company`, ✅ `DemoSeeder` con admin/evaluator/auditor.

**Pendiente para cerrar el 4º rol:**

1. En `User`: agregar `isUser(): bool { return $this->role === 'user'; }` y el gate
   `Gate::define('user', fn (User $u) => $u->isUser())` en `AppServiceProvider`.
2. En `RegisteredUserController::store()`: cambiar `'role' => 'evaluator'` por
   `'role' => 'user'` (el auto-registro genera clientes free, no staff).
3. En `AssessmentPolicy`: tratar a `user` igual que a `evaluator` para sus propias empresas
   (`view`/`create`/`update` sobre `user_id` propio). La diferencia entre ambos no está en los
   permisos básicos sino en origen, plan default y visibilidad para auditores.
4. En `DemoSeeder`: agregar un usuario demo con rol `user` (plan `free`) para mostrar el
   contraste free vs pro en la demo.
5. Dashboard ramificado por rol (ver tabla abajo).

**Qué ve cada rol en el dashboard (`DashboardController::index()`):**

| Rol | Empresas | Evaluaciones | Botón "Nuevo diagnóstico" | Columna "Evaluador" |
|---|---|---|---|---|
| `admin` | Todas | Todas | ✅ | ✅ |
| `evaluator` | Propias + asignadas | De sus empresas | ✅ | ❌ |
| `user` | Solo propias | Solo propias | ✅ | ❌ |
| `auditor` | Asignadas | De asignadas | ❌ (solo lectura) | ❌ |

```php
// DashboardController::index() — ramificación por rol
if ($user->isAdmin()) {
    $companies = Company::all();
    $assessmentsQuery = Assessment::with(['company', 'user']);
} elseif ($user->isAuditor()) {
    $companies = $user->auditedCompanies;
    $assessmentsQuery = Assessment::whereIn('company_id', $companies->pluck('id'))->with('company');
} else { // user | evaluator
    $companies = Company::where('user_id', $user->id)->get();
    $assessmentsQuery = Assessment::where('user_id', $user->id)->with('company');
}
```

#### 2.4-bis — Plan free / pro (eje de monetización)

> **Concepto clave:** `role` (autorización) y `plan` (monetización) son **ejes ortogonales**.
> Para el `user` self-service mapean naturalmente (user → free por default), pero se modelan
> por separado para poder hacer upgrade a `pro` sin cambiar el rol, y para que evaluator/admin
> sean `pro` independientemente de su rol.

```
role  ->  admin | evaluator | auditor | user   (QUÉ puede hacer)
plan  ->  free  | pro                          (CUÁNTO obtiene)
```

| Plan | Incluye |
|---|---|
| `free` | Diagnóstico + score + gauge + brechas + recomendaciones por **reglas** + informe básico de % de cumplimiento. **Sin IA.** No visible a auditores. |
| `pro` | Todo lo de free + **explicación de cada pregunta con IA** + **interpretación de resultados** + **plan de acción enriquecido por IA** (informe ejecutivo del LLM experto en Ley 1581). |

**Implementación:**

1. Migración `add_plan_to_companies_table`:
   ```php
   $table->string('plan')->default('free')->after('size');
   ```
2. En `Company`, helpers:
   ```php
   public function isPro(): bool { return $this->plan === 'pro' || config('cumplia.demo_mode'); }
   public function isFree(): bool { return ! $this->isPro(); }
   ```
3. **Modo demo (clave para el pitch):** crear `config/cumplia.php`:
   ```php
   return ['demo_mode' => env('CUMPLIA_DEMO_MODE', true)];
   ```
   Con `demo_mode = true`, TODA empresa se trata como `pro` → en la demo ambos enfoques
   (free y pro) son gratuitos y la IA está disponible. En producción se pone
   `CUMPLIA_DEMO_MODE=false` y el gating se activa solo, sin tocar lógica. En el pitch:
   *"la plataforma ya está lista para monetizar; solo se togglea un flag, sin construir
   pasarela de pago hoy"*.
4. **Gating de IA:** en `IAController` (explicar pregunta / interpretar) y en
   `DiagnosticController` (recomendaciones IA + `ai_summary`), antes de invocar `AIService`
   verificar `$assessment->company->isPro()`. Si es `free`: omitir el enriquecimiento IA,
   entregar solo el resultado por reglas y mostrar un CTA "Mejorá a Pro para análisis con IA"
   (en demo no aparece porque `demo_mode` fuerza pro).

**Para el pitch (innovación — 10% de la nota):** modelo de negocio freemium. El `user` se
registra gratis y obtiene su diagnóstico básico; la conversión a `pro` desbloquea el consultor
IA experto en Ley 1581. Los 3 roles del reto (admin/evaluator/auditor) cubren el flujo
B2B/consultoría con auditoría profesional; el 4º rol `user` + plan free/pro es el motor de
monetización B2C.

#### 2.5 — Multiempresa con pivot auditor-empresa

Crear migración `create_auditor_company_table`:
```php
Schema::create('auditor_company', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->unique(['user_id', 'company_id']);
    $table->timestamps();
});
```

En `User`, agregar:
```php
public function auditedCompanies(): BelongsToMany
{
    return $this->belongsToMany(Company::class, 'auditor_company');
}
```

Crear ruta y vista de administración (solo rol `admin`) en `/admin`:
- Ver todas las empresas.
- Asignar auditores a empresas.
- Ver todas las evaluaciones.

#### 2.6 — PDF descargable

Instalar:
```bash
composer require barryvdh/laravel-dompdf
```

Crear vista `resources/views/diagnostic/report.blade.php`:
- Es la misma información que `results.blade.php` pero con estilos inline (CSS `style=`)
  porque dompdf no usa clases Tailwind.
- Incluir: logo/nombre CumplIA, empresa, fecha, score, gauge simplificado (texto),
  desglose por bloque, brechas, recomendaciones.

En `DiagnosticController`, agregar método `exportPdf`:
```php
public function exportPdf(Assessment $assessment)
{
    $this->authorizeAccess($assessment);
    // cargar datos igual que results()
    $pdf = Pdf::loadView('diagnostic.report', compact('assessment', 'categoryResults', 'gaps'));
    return $pdf->download("diagnostico-{$assessment->company->name}-{$assessment->id}.pdf");
}
```

Ruta:
```php
Route::get('/diagnostic/{assessment}/report', [DiagnosticController::class, 'exportPdf'])->name('diagnostic.report');
```

Botón en `results.blade.php`:
```blade
<a href="{{ route('diagnostic.report', $assessment) }}" class="...">
    Descargar PDF
</a>
```

#### 2.7 — Seguridad (checklist del jurado)

1. **Rate limiting en login**: en `bootstrap/app.php`, dentro de `->withMiddleware()`:
   ```php
   $middleware->throttleApiWithRedis(); // o usar throttle estándar
   ```
   O directamente en la ruta de login en `routes/auth.php`: `->middleware('throttle:5,1')`.

2. **Form Requests**: convertir validaciones inline de `CompanyController` y
   `DiagnosticController` a Form Request classes con `php artisan make:request`.

3. **`.gitignore`**: confirmar que `.env` y `database/database.sqlite` estén en `.gitignore`.

4. **Headers de seguridad**: en `AppServiceProvider`, agregar middleware para headers
   `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`.

---

## Fase 3 — Deploy + Pulido Demo · Estimado: 1-2h

### Deploy en Railway (recomendado para demo)

1. Crear `Procfile`:
   ```
   web: php artisan serve --host=0.0.0.0 --port=$PORT
   ```

2. Crear `railway.json` (release command):
   ```json
   {
     "$schema": "https://railway.app/railway.schema.json",
     "build": { "builder": "NIXPACKS" },
     "deploy": {
       "releaseCommand": "php artisan migrate --force && php artisan db:seed --class=DiagnosticSeeder --force",
       "restartPolicyType": "ON_FAILURE"
     }
   }
   ```

3. Variables de entorno en Railway:
   ```
   APP_KEY=<generar con php artisan key:generate --show>
   APP_ENV=production
   APP_DEBUG=false
   DB_CONNECTION=sqlite
   GEMINI_API_KEY=<tu key>
   GOOGLE_CLIENT_ID=<si tenés>
   GOOGLE_CLIENT_SECRET=<si tenés>
   GOOGLE_REDIRECT_URI=https://<dominio-railway>/auth/google/callback
   ```

4. SQLite en producción: la carpeta `database/` debe existir y el archivo `database.sqlite`
   también. En `AppServiceProvider::boot()` agregar:
   ```php
   if (!file_exists(database_path('database.sqlite'))) {
       touch(database_path('database.sqlite'));
   }
   ```

### Fallback anti-pánico (si el deploy falla)

```bash
# Túnel Cloudflare — expone tu localhost con URL pública real
cloudflared tunnel --url http://localhost:8000
```

No requiere cuenta. Genera URL pública instantánea para la demo en vivo.

### Checklist final antes de la demo

- [ ] `php artisan migrate:fresh --seed` corre sin errores
- [ ] Flujo completo end-to-end sin errores 500: registro → empresa → diagnóstico → resultados
- [ ] Bug `\$barWidth` arreglado (barras de progreso visibles)
- [ ] Preguntas padre-hijo con lógica condicional (Alpine.js)
- [ ] Recomendaciones visibles con badges de prioridad
- [ ] Score gauge muestra % correcto con color según nivel
- [ ] `.env` no comiteado / sin secrets en el código
- [ ] PDF descargable (si Fase 2 completada)
- [ ] Login con Google funciona (si Fase 1.4 completada)
- [ ] URL de deploy activa y accesible

### Orden del pitch de demo

1. Landing → presentar CumplIA y tagline
2. Login / registro
3. Crear empresa (nombre, NIT, sector, tamaño)
4. Iniciar diagnóstico → mostrar preguntas con lógica condicional
5. Responder y enviar → resultados con gauge visual
6. Mostrar desglose por bloque + brechas + plan de acción
7. Botón "?" explicación IA en alguna pregunta (si Fase 2)
8. Descargar PDF (si Fase 2)
9. Dashboard con histórico de evaluaciones

---

## Notas para el auditor (Claude, Fase de verificación)

Al revisar la implementación de DeepSeek, verificar:

1. **Scoring no roto**: `calculateScore()` en `DiagnosticController` debe devolver 0–100.
   No debe sumar Q1 (peso=0, `is_complementary=false`) ni Q11 (`is_complementary=true`).
   Total de pesos contables: 10+10+10+10+12+12+12+16+8 = **100** exacto.

2. **Bug `\$` corregido**: en `results.blade.php` no deben quedar backslashes en variables PHP
   dentro de `@php`.

3. **API key nunca en frontend**: `GEMINI_API_KEY` solo en `.env` y `config/services.php`.
   Si aparece en alguna vista Blade o respuesta JSON pública → falla de seguridad crítica.

4. **Migraciones portables**: verificar que no haya SQL raw con sintaxis SQLite-específica.
   Todas las migraciones deben usar el Schema Builder de Laravel.

5. **Lógica condicional de preguntas**: Q2-Q5 deben tener `x-show` ligado a la respuesta
   de Q1. Q11 debe tener `x-show` ligado a Q10. Sin esto el cuestionario es engañoso.

6. **Recomendaciones ordenadas por prioridad**: `high` → `medium` → `low` en la vista.

7. **Sin API Controller sin usar**: `app/Http/Controllers/Api/DiagnosticController.php`
   y `app/Http/Controllers/Api/AuthController.php` no deben haber sido tocados ni expandidos
   (son deuda técnica, no parte del entregable).
