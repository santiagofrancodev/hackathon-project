# CumplIA — Contexto Maestro del Proyecto
### Hackathon CAVALTEC / Talento Tech · 2 días

> **Cómo usar este archivo:** Renómbralo `CLAUDE.md` en la raíz de cualquiera de los dos repos (frontend o backend). El contexto específico de cada repo va en su propia sección al final. Léelo completo antes de generar código.

---

## 1. Identidad del equipo y del producto

### Nombre: CumplIA

"Cumple" + "IA" en una sola palabra. El jurado lo entiende en el segundo 1 del pitch, sin que nadie tenga que explicarlo. Para un hackathon de 2 días, eso vale más que un nombre de marca elegante que necesita contexto.

### Tagline

> *"De la incertidumbre legal a un plan de acción priorizado, en minutos — sin necesitar un abogado de planta."*

### Por qué existimos

La Ley 1581 de 2012 es de obligatorio cumplimiento en Colombia para cualquier persona natural o jurídica que trate datos personales. La Superintendencia de Industria y Comercio (SIC) sanciona su incumplimiento con multas de hasta 2.000 SMMLV. El problema: hoy solo hay dos opciones para una pyme — contratar una consultoría costosa, o no hacer nada. CumplIA es la tercera opción: autodiagnóstico guiado por IA, gratuito, en minutos.

### A quién le hablamos

- **Pymes colombianas (10–200 empleados)** en sectores que manejan datos sensibles: salud, fintech, RRHH/BPO, retail con e-commerce.
- **Consultores y contadores** que asesoran a varias pymes a la vez → por eso el rol `auditor` no es un detalle técnico menor, es el gancho para este segundo segmento.

### Paleta de colores y tipografía (para Tailwind config)

| Token | Hex | Uso |
|---|---|---|
| `brand-navy` | `#1E3A5F` | Fondo de nav, headers, botones primarios |
| `brand-emerald` | `#10B981` | Gauge de cumplimiento alto, badges de éxito |
| `brand-amber` | `#F59E0B` | Cumplimiento medio, alertas |
| `brand-red` | `#EF4444` | Cumplimiento bajo, brechas críticas |
| `brand-slate` | `#F8FAFC` | Fondo general de la app |
| `brand-text` | `#1E293B` | Texto principal |

Tipografía: **Inter** (Google Fonts) — `font-sans` de Tailwind, sin cambios. No perder tiempo en tipografía custom en un hackathon.

---

## 2. Qué estamos construyendo

App web de **autodiagnóstico de cumplimiento de la Ley 1581 de 2012** para PYMEs. Un cuestionario de 11 preguntas (agrupadas en 3 bloques) calcula un % de cumplimiento, identifica brechas y usa IA para explicar preguntas y generar un plan de acción priorizado.

**Scope del hackathon — lo que NO construimos:**
- Sistema de pagos, planes, billing, onboarding comercial tipo SaaS
- Multi-tenancy con subdominios o bases de datos separadas por empresa
- Chat con memoria persistente o múltiples turnos
- Animaciones decorativas que no aporten a los criterios de evaluación

---

## 3. Arquitectura general

```
┌─────────────────────────────────────────────────────────────────┐
│                        USUARIO (Navegador)                       │
└───────────────────────────────┬─────────────────────────────────┘
                                │ HTTPS
┌───────────────────────────────▼─────────────────────────────────┐
│              FRONTEND — React + Vite + Tailwind                  │
│  React Router / Context API / Recharts / fetch + interceptor     │
└───────────────────────────────┬─────────────────────────────────┘
                                │ REST JSON  (token Sanctum)
┌───────────────────────────────▼─────────────────────────────────┐
│              BACKEND — Laravel 11 + PostgreSQL                   │
│  Sanctum · Socialite · ScoringService · Policies · Form Requests │
└──────────────┬──────────────────────────────────────────────────┘
               │ HTTPS (server-side, API key nunca llega al browser)
┌──────────────▼──────────────┐
│    IA — Gemini Flash API     │
│  (explicar-pregunta,         │
│   recomendaciones,           │
│   preguntar-libre)           │
└─────────────────────────────┘
```

### Decisiones de arquitectura que no hay que cuestionar durante el hackathon

1. **Un solo dominio / base de datos.** Multiempresa = `empresa_id` en cada tabla. No subdominios, no schemas separados.
2. **Sanctum para tokens.** No JWT propio, no OAuth custom. Sanctum + Socialite es el camino más corto.
3. **IA en el backend.** La API key de Gemini nunca toca el frontend. Todos los llamados a IA van por endpoints de Laravel.
4. **Estado en Context API.** No Redux. Dos contextos: `AuthContext` y `EmpresaContext`.
5. **Cálculo de score solo en el backend.** El frontend nunca suma pesos — solo muestra lo que devuelve `/resultado`.

---

## 4. Modelo de datos (PostgreSQL)

```sql
-- empresas
id, nombre, nit, sector, tamano, created_at

-- users
id, name, email, provider, provider_id,
role ENUM('administrador','evaluador','auditor'),
empresa_id FK->empresas (null para admin/auditor globales)

-- auditor_empresa (pivot)
user_id FK->users, empresa_id FK->empresas

-- preguntas
id, bloque ENUM('politica_datos','privacidad_diseno','gobernanza'),
parent_id FK->preguntas (nullable),
texto, peso DECIMAL, counts_toward_total BOOLEAN, orden

-- evaluaciones
id, empresa_id, user_id, estado ENUM('borrador','completada'),
porcentaje_total DECIMAL (null hasta finalizar),
created_at, completed_at

-- respuestas
id, evaluacion_id, pregunta_id, valor BOOLEAN, comentario

-- recomendaciones
id, evaluacion_id, pregunta_id (nullable),
texto, prioridad ENUM('alta','media','baja'),
origen ENUM('ia','regla')
```

---

## 5. Las 11 preguntas — pesos exactos (no improvisar)

| ID | Bloque | Parent | Pregunta | Peso | Cuenta al total |
|---|---|---|---|---|---|
| Q1 | politica_datos | — | ¿Cuenta con una política de tratamiento de datos personales? | 0–40% (heredado) | **NO** (es resumen) |
| Q2 | politica_datos | Q1 | ¿La política está documentada y publicada en medio de fácil acceso? | 10% | SÍ |
| Q3 | politica_datos | Q1 | ¿Define las finalidades del tratamiento de datos? | 10% | SÍ |
| Q4 | politica_datos | Q1 | ¿Incluye los derechos de los titulares? | 10% | SÍ |
| Q5 | politica_datos | Q1 | ¿Menciona cómo ejercer los derechos de los titulares? | 10% | SÍ |
| Q6 | privacidad_diseno | — | ¿Incorpora evaluaciones de impacto (PIA)? | 12% | SÍ |
| Q7 | privacidad_diseno | — | ¿Aplica técnicas de minimización de datos? | 12% | SÍ |
| Q8 | privacidad_diseno | — | ¿Configura sus sistemas para recopilar el mínimo de datos por defecto? | 12% | SÍ |
| Q9 | gobernanza | — | ¿Cuenta con un sistema de administración de riesgos? | 16% | SÍ |
| Q10 | gobernanza | — | ¿Cuenta con un oficial de protección de datos personales? | 8% | SÍ |
| Q11 | gobernanza | Q10 | ¿Está designado formalmente? | — | **NO** (complementaria) |

**Verificación:** 10+10+10+10 + 12+12+12 + 16+8 = **100%** exacto.

**Error crítico a evitar:** Si se suma Q1 (0–40%) MÁS Q2–Q5 (10% c/u), el total se duplica y nunca da 100%. El backend tiene un test unitario que verifica esto desde el día 1.

---

## 6. Contrato de API (compartido — no cambiar sin avisar al compañero de equipo)

```
# AUTH
GET  /api/auth/redirect/{provider}        → redirige a OAuth (google)
GET  /api/auth/callback/{provider}        → { token, user }
GET  /api/me                              → { user, role, empresa }

# EMPRESAS
POST /api/empresas   { nombre, nit, sector, tamano }   → empresa
GET  /api/empresas                                     → [empresa]

# PREGUNTAS
GET  /api/preguntas  → { bloques: [ { nombre, preguntas: [{id, texto, peso, hijas:[...]}] } ] }

# EVALUACIONES
POST /api/evaluaciones              { empresa_id }                → evaluacion (estado=borrador)
GET  /api/evaluaciones?empresa_id=                               → [evaluacion]
GET  /api/evaluaciones/{id}                                      → evaluacion + respuestas
POST /api/evaluaciones/{id}/respuestas  { respuestas: [{pregunta_id, valor}] }
POST /api/evaluaciones/{id}/finalizar
GET  /api/evaluaciones/{id}/resultado   → { porcentaje_total, por_bloque, brechas }
GET  /api/evaluaciones/{id}/recomendaciones → { recomendaciones: [...] }
GET  /api/evaluaciones/{id}/reporte.pdf → descarga binaria

# IA
POST /api/ia/explicar-pregunta   { pregunta_id }              → { explicacion }
POST /api/ia/preguntar-libre     { evaluacion_id, pregunta }  → { respuesta }
```

---

## 7. Roles y permisos

| Rol | Puede hacer |
|---|---|
| `administrador` | CRUD de empresas y usuarios, ver todas las evaluaciones |
| `evaluador` | Crear/responder evaluaciones de **su propia** empresa, ver su histórico |
| `auditor` | Solo lectura de evaluaciones/reportes de las empresas que tenga asignadas en `auditor_empresa` |

**Regla de oro:** La seguridad real vive en el backend (Policies de Laravel). El frontend solo oculta/muestra con `RoleGate` — eso es cosmético, no seguridad.

---

## 8. Plan de fases — 2 días de hackathon

### Fase 0 — Setup conjunto (~2-3h, día 1 mañana)
- [ ] Repos creados, ramas `main` y `dev`
- [ ] Frontend: Vite + React + Tailwind + React Router corriendo
- [ ] Backend: Laravel + PostgreSQL conectado, migraciones de las 7 tablas
- [ ] Seeder con las 11 preguntas exactas de la sección 5
- [ ] `tailwind.config` con la paleta de la sección 1
- [ ] Cliente API en `services/api.js` con interceptor de token (aunque aún mockee)
- [ ] Acuerdo sobre URL base: `http://localhost:8000/api`

### Fase 1 — Nivel básico (día 1, tarde) 🥇
**Backend**
- [ ] `GET /preguntas` devuelve árbol con hijas anidadas
- [ ] `POST /evaluaciones`, `POST /evaluaciones/{id}/respuestas`
- [ ] `ScoringService` con test unitario (el más importante del proyecto)
- [ ] `GET /evaluaciones/{id}/resultado`

**Frontend**
- [ ] Landing simple con CTA "Iniciar diagnóstico"
- [ ] Login con mock (usuario fijo, sin OAuth)
- [ ] Formulario dinámico que respeta la jerarquía de preguntas (Q1 como encabezado, Q2–Q5 indentadas; Q11 condicional)
- [ ] Pantalla de resultado con **Gauge** (Recharts `RadialBarChart`) mostrando % y desglose por bloque
- **Meta:** flujo completo funcional aunque sea con mocks

### Fase 2 — Nivel intermedio (día 1, noche) 🥇🥇
**Backend**
- [ ] Socialite (Google) + Sanctum: token real al loguearse
- [ ] Middleware/Policy de roles activos
- [ ] `GET /evaluaciones?empresa_id=` (histórico)
- [ ] Recomendaciones por reglas (texto fijo por pregunta en `false`, sin IA aún)

**Frontend**
- [ ] Botón "Iniciar con Google" → flujo OAuth real
- [ ] Dashboard: cards de evaluaciones pasadas + gráfico de línea histórico (Recharts)
- [ ] Lógica condicional: mostrar/ocultar hijas de Q1 según respuesta del usuario
- [ ] Recomendaciones básicas debajo del resultado

### Fase 3 — Nivel avanzado (día 2, mañana) 🥇🥇🥇
**Backend**
- [ ] Scoping multiempresa real + pivot `auditor_empresa`
- [ ] Integración Gemini Flash: `explicar-pregunta` y `preguntar-libre`
- [ ] Plan de acción priorizado (IA genera prioridad alta/media/baja a partir de brechas)
- [ ] Reporte PDF descargable (`barryvdh/laravel-dompdf`)

**Frontend**
- [ ] Selector de empresa para rol auditor
- [ ] Vistas diferenciadas por rol (`RoleGate`)
- [ ] `ModalExplicacionIA`: botón "?" junto a cada pregunta → llama `/ia/explicar-pregunta`
- [ ] Plan de acción con badges de prioridad (alta=rojo, media=ámbar, baja=verde)
- [ ] `ChatLibreIA`: caja pregunta→respuesta (sin historial, una sola vuelta)
- [ ] Botón descarga reporte PDF

### Fase 4 — Pulido y demo (día 2, tarde)
- [ ] Responsive (mínimo: se ve bien en laptop del jurado)
- [ ] Loading states y manejo de errores en cada llamada
- [ ] Microcopy en español claro, sin jerga legal
- [ ] Seguridad: Form Requests en todos los endpoints de escritura, rate limiting en login, confirmar que Policies bloquean acceso cross-empresa
- [ ] Orden del video/demo: Landing → Login → Formulario → Resultado con Gauge → Plan de acción IA → Dashboard/Histórico → Descarga PDF

---

## 9. Estructura de carpetas

### Frontend (`/src`)
```
pages/
  Landing.jsx
  Login.jsx
  Onboarding.jsx        ← registro de empresa
  Cuestionario.jsx
  Resultado.jsx
  Dashboard.jsx
  Admin.jsx

components/
  Gauge.jsx             ← RadialBarChart de Recharts
  PreguntaItem.jsx      ← renderiza una pregunta + hijas si las tiene
  BloqueCard.jsx
  RecomendacionCard.jsx ← badge de prioridad incluido
  ModalExplicacionIA.jsx
  ChatLibreIA.jsx
  RoleGate.jsx          ← { role, children } — oculta si no coincide

hooks/
  useAuth.js
  useEvaluacion.js
  usePreguntas.js

services/
  api.js                ← axios con interceptor de token Sanctum

context/
  AuthContext.jsx
  EmpresaContext.jsx
```

### Backend (`/app`)
```
Http/
  Controllers/Api/
    AuthController.php
    EmpresaController.php
    PreguntaController.php
    EvaluacionController.php
    ResultadoController.php
    RecomendacionController.php
    IAController.php
  Requests/              ← Form Requests de validación
  Policies/              ← EvaluacionPolicy, EmpresaPolicy

Models/
  User.php, Empresa.php, Pregunta.php,
  Evaluacion.php, Respuesta.php, Recomendacion.php

Services/
  ScoringService.php    ← el más importante — tiene test unitario
  IAService.php         ← wrapper de Gemini Flash
  PDFService.php        ← wrapper de dompdf
```

---

## 10. Motor de scoring — pseudocódigo de referencia

```php
// ScoringService::calcular(Evaluacion $evaluacion): array

$preguntasContables = Pregunta::where('counts_toward_total', true)->get();
$porcentajeTotal = 0;
$porBloque = ['politica_datos' => 0, 'privacidad_diseno' => 0, 'gobernanza' => 0];
$brechas = [];

foreach ($preguntasContables as $p) {
    $respuesta = Respuesta::where('evaluacion_id', $evaluacion->id)
                          ->where('pregunta_id', $p->id)->first();
    $obtenido = ($respuesta && $respuesta->valor) ? $p->peso : 0;
    $porcentajeTotal += $obtenido;
    $porBloque[$p->bloque] += $obtenido;
    if ($obtenido === 0) $brechas[] = $p;
}

// Q1 no se cuenta — su valor mostrado es $porBloque['politica_datos']
// Q11 nunca entra en el cálculo — solo se guarda como metadato de Q10
return compact('porcentajeTotal', 'porBloque', 'brechas');
```

---

## 11. Criterios de evaluación y cómo cubrirlos

| Criterio | Peso | Dónde lo cubrimos |
|---|---|---|
| Alineación con la Ley 1581 | 20% | Preguntas exactas del reto, ScoringService correcto, lenguaje legal en las explicaciones de IA |
| Desarrollo técnico | 20% | Arquitectura limpia, separación de responsabilidades, test unitario del scoring |
| Seguridad | 15% | Sanctum + Socialite, Policies, Form Requests, rate limiting, sin secrets en repo |
| Uso de IA | 15% | ModalExplicacionIA, plan de acción priorizado, ChatLibreIA |
| Experiencia de usuario | 10% | Gauge visual, microcopy claro, flujo sin fricciones |
| Calidad del diagnóstico | 10% | Desglose por bloque, identificación de brechas, recomendaciones accionables |
| Innovación | 10% | IA que traduce lenguaje legal, plan priorizado automático, rol auditor multiempresa |

---

## 12. Mocks de API para desarrollo desacoplado

Mientras el backend no esté listo, usar estos shapes exactos en el frontend:

```js
// GET /api/preguntas
{
  bloques: [
    {
      nombre: "Política de datos personales",
      preguntas: [
        {
          id: 1, texto: "¿Cuenta con una política de tratamiento de datos personales?",
          peso: 0, counts_toward_total: false,
          hijas: [
            { id: 2, texto: "¿La política está documentada y publicada...?", peso: 10, hijas: [] },
            { id: 3, texto: "¿Define las finalidades...?", peso: 10, hijas: [] },
            { id: 4, texto: "¿Incluye los derechos de los titulares?", peso: 10, hijas: [] },
            { id: 5, texto: "¿Menciona cómo ejercer los derechos?", peso: 10, hijas: [] }
          ]
        }
      ]
    },
    {
      nombre: "Privacidad desde el diseño",
      preguntas: [
        { id: 6, texto: "¿Incorpora evaluaciones de impacto (PIA)?", peso: 12, hijas: [] },
        { id: 7, texto: "¿Aplica técnicas de minimización de datos?", peso: 12, hijas: [] },
        { id: 8, texto: "¿Configura sus sistemas para recopilar el mínimo...?", peso: 12, hijas: [] }
      ]
    },
    {
      nombre: "Gobernanza",
      preguntas: [
        { id: 9, texto: "¿Cuenta con un sistema de administración de riesgos?", peso: 16, hijas: [] },
        {
          id: 10, texto: "¿Cuenta con un oficial de protección de datos?", peso: 8,
          hijas: [
            { id: 11, texto: "¿Está designado formalmente?", peso: 0, counts_toward_total: false, hijas: [] }
          ]
        }
      ]
    }
  ]
}

// GET /api/evaluaciones/{id}/resultado
{
  porcentaje_total: 68,
  por_bloque: {
    politica_datos: 30,
    privacidad_diseno: 24,
    gobernanza: 14
  },
  brechas: [
    { id: 4, texto: "¿Incluye los derechos de los titulares?", peso: 10, bloque: "politica_datos" },
    { id: 9, texto: "¿Cuenta con un sistema de administración de riesgos?", peso: 16, bloque: "gobernanza" }
  ]
}

// GET /api/evaluaciones/{id}/recomendaciones
{
  recomendaciones: [
    { id: 1, pregunta_id: 4, texto: "Incluya en su política una sección...", prioridad: "alta", origen: "ia" },
    { id: 2, pregunta_id: 9, texto: "Implemente una matriz de riesgos...", prioridad: "alta", origen: "ia" }
  ]
}
```

---

## 13. Checklist de seguridad (lo que un jurado técnico revisa)

- [ ] Validación de entradas con Form Requests (no solo en el frontend)
- [ ] Sanctum + Socialite funcionando end-to-end
- [ ] Rate limiting en `/login` (`throttle:5,1`)
- [ ] Policies de Laravel aplicadas — probadas con Postman cross-empresa
- [ ] `.env` en `.gitignore`, sin keys hardcodeadas en el código
- [ ] Queries siempre filtradas por `empresa_id` en el backend
- [ ] CORS configurado solo para el origen del frontend