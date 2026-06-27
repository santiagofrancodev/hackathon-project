# Datacheck AI — Documentación de Vistas y Estilos

> **Proyecto:** Datacheck AI (Next.js + React + Tailwind CSS)  
> **Propósito:** Consolidar la paleta, componentes y patrones visuales del frontend React para migrar/replicar estilos en el proyecto Laravel + Blade (Vue-like).  
> **Paleta oficial:** CAVALTEC — Light Mode  
> **Backend compartido:** Misma API que el proyecto Laravel/Breeze.

---

## 1. Paleta de Colores (CSS Custom Properties)

Todos los valores están definidos en `app/globals.css` como variables CSS en `:root`.

### 1.1 Fondos

| Variable | Valor Hex | Uso |
|----------|-----------|-----|
| `--color-bg-base` | `#F8FAFC` | Fondo general de páginas |
| `--color-bg-surface` | `#FFFFFF` | Tarjetas, cards, paneles |
| `--color-bg-muted` | `#F1F5F9` | Fondo de inputs deshabilitados, áreas sutiles |

### 1.2 Bordes

| Variable | Valor Hex | Uso |
|----------|-----------|-----|
| `--color-border` | `#E2E8F0` | Borde default de inputs, cards, tablas |
| `--color-border-focus` | `#2563EB` | Borde en estado focus |

### 1.3 Textos

| Variable | Valor Hex | Uso |
|----------|-----------|-----|
| `--color-text-primary` | `#0F172A` | Títulos, cuerpo principal |
| `--color-text-secondary` | `#64748B` | Subtítulos, labels, helper text |
| `--color-text-muted` | `#94A3B8` | Placeholder, texto deshabilitado |
| `--color-text-inverse` | `#FFFFFF` | Texto sobre fondos oscuros |

### 1.4 Marca / Primario

| Variable | Valor Hex | Uso |
|----------|-----------|-----|
| `--color-primary` | `#2563EB` | Botón primario, links, acentos |
| `--color-primary-hover` | `#1D4ED8` | Hover de botón primario |
| `--color-primary-light` | `#EFF6FF` | Fondo de badges/consejos azules |
| `--color-ia` | `#3B82F6` | Elementos de IA/Copilot |
| `--color-sidebar-from` | `#041C4A` | Gradiente sidebar (inicio) |
| `--color-sidebar-to` | `#0A2E73` | Gradiente sidebar (fin) |

### 1.5 Niveles de Riesgo (semántico)

| Nivel | Color | Fondo | Borde | ícono |
|-------|-------|-------|-------|-------|
| Conforme | `#16A34A` | `#DCFCE7` | `#BBF7D0` | ✅ |
| En Proceso | `#F59E0B` | `#FEF3C7` | `#FDE68A` | ⚠️ |
| Crítico | `#EF4444` | `#FEE2E2` | `#FECACA` | 🚨 |

### 1.6 Transiciones

| Variable | Valor | Uso |
|----------|-------|-----|
| `--transition-fast` | `150ms ease` | hover, focus |
| `--transition-base` | `250ms ease` | Transiciones generales |
| `--transition-slow` | `500ms ease` | Animaciones complejas |

---

## 2. Tipografía

- **Familia principal:** Inter (Google Fonts) — `var(--font-inter)`
- **Font loading:** `display: swap`, `subsets: ['latin']`
- **Alias CSS:** `var(--font-sans)` apunta a Inter con fallbacks `system-ui, -apple-system, sans-serif`
- **Pesos usados:**
  - `font-black` (900) — títulos principales, scores grandes, logo
  - `font-bold` (700) — labels de inputs, badges, botones, subtítulos
  - `font-semibold` (600) — textos de nav, pills, helper text importante
  - `font-medium` (500) — texto normal con énfasis
  - `text-xs` (12px) — helper text, labels pequeños, timestamps
  - `text-sm` (14px) — cuerpo general, botones, input labels
  - `text-base` (16px) — emojis en iconos
  - `text-lg` a `text-4xl` — títulos de página

- **Tracking:** `tracking-tight` (logo), `tracking-wider` (labels uppercase), `tracking-widest` (etiquetas mono)
- **Font mono:** Para NITs, IDs de diagnóstico, código legal, progreso — `font-mono`

---

## 3. Clases CSS Utilitarias Propias (globals.css)

### 3.1 Animaciones

| Clase | Descripción | Duración |
|-------|-------------|----------|
| `.animate-fade-slide-up` | Fade + slide hacia arriba (entrada de cards) | 0.45s |
| `.animate-fade-slide-right` | Fade + slide desde la derecha | 0.35s |
| `.animate-shimmer` | Shimmer sweep (progress bar con gradiente) | 2.5s loop |
| `.animate-glow-pulse-blue` | Pulso azul glow (indicadores live) | 2s loop |
| `.animate-scale-in` | Scale in (modales, alertas) | 0.25s |
| `.animate-float` | Float suave vertical | 3s loop |
| `.animate-stagger` | Stagger para hijos (delay por nth-child) | — |

**Curvas de ease:** `cubic-bezier(0.16, 1, 0.3, 1)` — salida suave tipo "spring-out"

### 3.2 Componentes Base

| Clase | Propósito | Detalles |
|-------|-----------|----------|
| `.sidebar-gradient` | Fondo del sidebar | `linear-gradient(180deg, #041C4A, #0A2E73)` |
| `.card` | Card base | `bg-white`, `border: 1px solid #E2E8F0`, `border-radius: 12px` |
| `.input-base` | Input base | Fondo blanco, borde `#E2E8F0`, radius 8px, focus con ring azul 3px opacity 12% |
| `.btn-primary` | Botón primario | Fondo `--color-primary`, blanco, radius 8px, box-shadow azul, hover translateY(-1px) |
| `.badge-conforme` | Badge verde | Fondo `#DCFCE7`, texto `#16A34A`, borde `#BBF7D0` |
| `.badge-proceso` | Badge amarillo | Fondo `#FEF3C7`, texto `#F59E0B`, borde `#FDE68A` |
| `.badge-critico` | Badge rojo | Fondo `#FEE2E2`, texto `#EF4444`, borde `#FECACA` |

### 3.3 Estilos de Impresión

- Oculta: `<aside>`, `<header>`, `<nav>`, `<button>`, `.no-print`, `#btn-descargar`, `#btn-reiniciar`, `#btn-ver-dashboard`, `.copilot-inline-container`, `.footer-legal-resources`, `<footer>`
- Reset de containers: `.flex`, `.grid` → `display: block`, `width: 100%`, sin margin/padding
- Fuerza colores de fondo: `-webkit-print-color-adjust: exact`
- Cards impresión: shadow none, borde 1px `#E2E8F0`, `page-break-inside: avoid`
- SVG max-width: 180px
- Grid de 3 columnas para métricas impresión

---

## 4. Patrones de Componente Reutilizables

### 4.1 Card genérica (handler en TSX)

```tsx
// Estructura base usada en:
// - PreguntaCard (diagnóstico)
// - ResultadoPanel (resultados)
// - MetricCard (métricas dashboard)
// - CopilotSidebar (panel lateral IA)

<CardWrapper>
  └── Header (icono + badge + contador)
  └── Body (contenido principal)
  └── Footer (acciones, si aplica)
</CardWrapper>
```

**Props comunes de card:**
- `rounded-2xl` — border-radius 16px (tarjetas principales)
- `rounded-xl` — border-radius 12px (tarjetas secundarias, bloques)
- `p-5` o `p-6` / `sm:p-8` — padding responsive
- `border border-[#E2E8F0]` — borde sutil
- `shadow-sm` o `shadow-lg` — elevación
- `bg-white` — fondo blanco
- `transition-all duration-200 hover:shadow-md hover:-translate-y-0.5` — hover lift

### 4.2 Badge / Pill

```tsx
// Patrón 1: Badge de bloque (PreguntaCard)
className="text-xs font-semibold px-2.5 py-1 rounded-full border"
// Con estilo dinámico por bloque (verde/azul/amarillo)

// Patrón 2: Badge de nivel riesgo (NIVEL_STYLE, MetricCard)
style={{ background: '...', color: '...', border: `1px solid ${...}` }}

// Patrón 3: Badge de rol de usuario (Sidebar)
className="px-1.5 py-0.5 rounded text-[9px] font-bold"
// Colores: admin → azul, evaluador → amber, auditor → emerald
```

### 4.3 Botones

**Primario:**
```tsx
className="btn-primary px-5 py-3 text-sm flex items-center gap-2"
// O inline-flex para links que parecen botones
className="btn-primary inline-flex items-center gap-2 px-5 py-3 text-sm no-underline"
```

**Secundario / Outline:**
```tsx
className="w-full py-3 px-4 flex items-center justify-center gap-3 
           rounded-lg border border-[#E2E8F0] bg-white 
           hover:bg-[#F8FAFC] hover:border-[#2563EB] hover:shadow-sm 
           text-sm font-semibold text-[#0F172A]"
// Usado para botón Google OAuth
```

**Botón deshabilitado (auditor):**
```tsx
className="bg-[#E2E8F0] border border-[#CBD5E1] text-[#94A3B8] 
           inline-flex items-center gap-2 px-5 py-3 text-sm 
           rounded-xl cursor-not-allowed font-semibold shadow-sm"
```

### 4.4 Inputs con validación

```tsx
// Base
className="input-base w-full px-4 py-3 text-sm"

// Error
className={`input-base ... ${error ? 'border-[#EF4444] focus:border-[#EF4444]' : ''}`}

// Éxito
className={`input-base ... ${valido ? 'border-[#16A34A]' : ''}`}

// Label pattern
className="block text-sm font-semibold text-[#0F172A] mb-1"
```

### 4.5 Input con toggle de visibilidad (contraseña)

```tsx
<div className="relative">
  <input className="input-base ... pr-12" ... />
  <button className="absolute right-3 top-1/2 -translate-y-1/2 
                     text-[#64748B] hover:text-[#0F172A] text-sm p-1">
    {show ? '🙈' : '👁️'}
  </button>
</div>
```

### 4.6 Progress Bar

```tsx
// Patrón usado en DiagnosticoWizard + ProgressBar.tsx
<div className="h-1.5 w-full rounded-full bg-[#E2E8F0] overflow-hidden">
  <div className="h-full rounded-full"
       style={{
         width: `${porcentaje}%`,
         background: 'linear-gradient(90deg, #1D4ED8, #2563EB 40%, #3B82F6 60%, #2563EB 100%)',
         backgroundSize: '200% 100%'
       }}
       className="animate-shimmer transition-all duration-500 ease-out" />
</div>
```

### 4.7 Layout de página autenticada (MainLayout)

```
┌──────────────────────────────────────────────────┐
│ Sidebar (lg:w-64, fijo)  │  Área de contenido   │
│                          │                       │
│  ┌──────────────────┐    │  Header mobile (lg:hidden) │
│  │ Sidebar          │    │  Main (scrollable)         │
│  │ - Logo           │    │                            │
│  │ - Selector emp   │    │                            │
│  │ - Nav items      │    │                            │
│  │ - Footer user    │    │                            │
│  └──────────────────┘    │                            │
└──────────────────────────────────────────────────┘
```

**Sidebar width:** `w-64` (256px)  
**Content max-width:** `max-w-6xl` (1152px), centrado con `mx-auto`  
**Padding page:** `p-6 lg:p-8`

### 4.8 Split-screen Login/Registro

```
┌─────────────────────────────────────────────────────────┐
│ Panel Izquierdo (lg:w-[55%])  │  Panel Derecho (flex-1) │
│ Fondo: gradiente navy         │  Fondo: #F8FAFC         │
│ - Logo                        │  - Logo mobile          │
│ - Hero text                   │  - Formulario centrado  │
│ - Features list               │                        │
│ - Badge legal                 │                        │
└─────────────────────────────────────────────────────────┘
// Mobile: solo se muestra panel derecho
// Patrón idéntico en /login y /registro
```

---

## 5. Mapa de Vistas

### 5.1 Vistas Públicas

| Ruta | Componente | Layout | Descripción |
|------|-----------|--------|-------------|
| `/` | `page.tsx` | root | Redirect a `/login` |
| `/login` | `login/page.tsx` | Split-screen | Login con branding + formulario |
| `/registro` | `registro/page.tsx` | Split-screen | Registro con pasos + formulario |

### 5.2 Vistas Autenticadas

| Ruta | Componente | Layout | Descripción |
|------|-----------|--------|-------------|
| `/dashboard` | `dashboard/page.tsx` | MainLayout + Sidebar | Panel principal con métricas e historial |
| `/diagnostico` | `diagnostico/page.tsx` | MainLayout + Sidebar | Wizard de diagnóstico |
| `/onboarding` | `onboarding/page.tsx` | Header propio | Captura datos empresa (paso previo) |

### 5.3 Componentes Shared

| Componente | Ruta | Uso |
|------------|------|-----|
| `Sidebar` | `app/ui/shared/Sidebar.tsx` | Nav lateral + selector empresa + user footer |
| `MetricCard` | `app/ui/shared/MetricCard.tsx` | Tarjeta de métrica con variante de riesgo |
| `AuthGuard` | `app/ui/auth/AuthGuard.tsx` | Protección de ruta autenticada |

### 5.4 Componentes Diagnóstico

| Componente | Ruta | Uso |
|------------|------|-----|
| `DiagnosticoWizard` | `app/ui/diagnostico/DiagnosticoWizard.tsx` | Orquestador del wizard |
| `PreguntaCard` | `app/ui/diagnostico/PreguntaCard.tsx` | Tarjeta de pregunta con botones Sí/No + Copilot inline |
| `CopilotSidebar` | `app/ui/diagnostico/CopilotSidebar.tsx` | Panel lateral derecho de chat IA |
| `ResultadoPanel` | `app/ui/diagnostico/ResultadoPanel.tsx` | Panel de resultados + gauge + plan acción |
| `GaugeChart` | `app/ui/diagnostico/GaugeChart.tsx` | Velocímetro SVG animado |
| `ProgressBar` | `app/ui/diagnostico/ProgressBar.tsx` | Barra de progreso shimmer |
| `SkipNotice` | `app/ui/diagnostico/SkipNotice.tsx` | Modal de aviso cuando se salta bloque |

### 5.5 Componentes Auth

| Componente | Ruta | Uso |
|------------|------|-----|
| `LoginForm` | `app/ui/auth/LoginForm.tsx` | Formulario login email + Google |
| `RegistroForm` | `app/ui/auth/RegistroForm.tsx` | Formulario registro con fortaleza password |
| `AuthGuard` | `app/ui/auth/AuthGuard.tsx` | Redirect si no autenticado + spinner loading |

---

## 6. Configuración por Bloque de Diagnóstico

Cada bloque tiene su propio gradiente y paleta semántica:

### Bloque: Política

```tsx
// BLOQUE_CONFIG['politica']
topBar:    'linear-gradient(90deg, transparent, #16A34A, transparent)' // Verde
numGradient: 'linear-gradient(135deg, #16A34A, #4ADE80)'
badgeBg:   '#DCFCE7', badgeColor: '#16A34A', badgeBorder: '#BBF7D0'
// Color acento: #16A34A (verde)
// Preguntas: Q1 — Q5
// Bloque con "skip notice" cuando Q1 = No
```

### Bloque: Privacidad y Diseño

```tsx
// BLOQUE_CONFIG['privacidad_disenio']
topBar:    'linear-gradient(90deg, transparent, #2563EB, transparent)' // Azul
numGradient: 'linear-gradient(135deg, #2563EB, #60A5FA)'
badgeBg:   '#EFF6FF', badgeColor: '#2563EB', badgeBorder: '#BFDBFE'
// Color acento: #2563EB (azul primario)
// Preguntas: Q6 — Q8
```

### Bloque: Gobernanza

```tsx
// BLOQUE_CONFIG['gobernanza']
topBar:    'linear-gradient(90deg, transparent, #F59E0B, transparent)' // Amber
numGradient: 'linear-gradient(135deg, #F59E0B, #FCD34D)'
badgeBg:   '#FEF3C7', badgeColor: '#F59E0B', badgeBorder: '#FDE68A'
// Color acento: #F59E0B (amber)
// Preguntas: Q9 — Q11
```

**Patrón de números gradiente en PreguntaCard:**
- Número de pregunta gigante (`text-6xl font-black`)
- `backgroundClip: 'text'` para efecto gradient sobre texto
- `WebkitBackgroundClip: 'text'` (fallback Safari)
- Color final: `transparent` con `backgroundImage` del gradiente del bloque

---

## 7. Patrones de Layout Específicos

### 7.1 Dashboard (page principal autenticada)

```
Contenedor: p-6 lg:p-8, max-w-6xl mx-auto, space-y-8

Secuencia:
1. Banner auditoría (si rol = auditor)
2. Header: título empresa + botón "Nuevo Diagnóstico"
3. Grid 4 columnas métricas (sm:grid-cols-2, lg:grid-cols-4)
   - Último Score, Score Promedio, Diagnósticos, Estados Críticos
4. Estado vacío (si no hay historial) — centrado con icono grande
5. Tabla historial (si hay datos) — grid 4 columnas: Fecha | Score | Estado | Brechas
   - Cabecera: bg-[#F8FAFC], text-[#64748B] uppercase, text-xs
   - Filas: hover:bg-[#F8FAFC], border-b separador
   - Badge estado inline con colores semánticos
6. Banner Ley 1581 (footer informativo) — gradiente navy sobre fondo oscuro
```

### 7.2 Diagnóstico Wizard

```
Header sticky: bg-white, border-b, z-10
  - Izquierda: breadcrumb (← | Empresa nombre)
  - Centro: ProgressBar (desktop: hidden md:block; mobile: mt-2 md:hidden)
  - Derecha: Score mini (rounded-xl, border, bg-[#F8FAFC])

Main: max-w-6xl, grid 2 columnas (lg:grid-cols-[1fr_280px])
  
  Columna Izquierda (1fr):
  - Bloque actual pill (icono + título + peso max)
  - PreguntaCard (principal)
  - Progreso por bloques (grid 3 columnas)
    Cada bloque: icono + contador (respondidas/total) + mini progress bar
  
  Columna Derecha (280px, sticky top-24):
  - GaugeCard (velocímetro)
  - Info Legal (texto normativo)
  - Tip Copilot (bg-[#EFF6FF])
```

### 7.3 CopilotSidebar (panel lateral)

```
Características:
- fixed, top-0, right-0, z-40, w-full max-w-md, h-full
- transform: translate-x-full (cerrado) → translate-x-0 (abierto)
- transition: duration-500 ease-out
- Background: bg-white, border-l, shadow-2xl

Estructura:
1. Header: gradiente navy (#041C4A → #0A2E73)
   - Icono 🤖 + título + estado "Listo/Analizando"
   - Botón cerrar (✕) circular blanco/transparente
2. Body: bg-[#F8FAFC], overflow-y-auto, px-5 py-5
   - Skeleton loading (pulsos grises)
   - Pregunta referenciada (card blanca)
   - Artículo legal (blockquote con borde amber)
   - Consejo Empresarial (bg-[#EFF6FF])
   - Ejemplo Práctico (bg-[#DCFCE7])
   - Disclaimer
   - Divider
   - Chat Interactivo (mensajes + input)
3. Footer: bg-white, border-t, shadow-[0_-4px...]
   - Input + botón Enviar (si contenido cargado)
   - O texto "Powered by Datacheck AI" (si cargando)
```

### 7.4 SkipNotice (modal)

```
- fixed inset-0, z-50, flex center
- pointer-events-none en wrapper (solo card tiene pointer-events-auto)
- Card: bg-[#FEF3C7], border-[#FDE68A], rounded-2xl, shadow-2xl con glow amber
- Auto-cierre: 4 segundos (shrink-width animation)
- Botón cerrar: circular en top-right
```

---

## 8. Sistema de Validación Visual

### 8.1 Estados de input

| Estado | Borde | Fondo | Texto helper |
|--------|-------|-------|--------------|
| Normal | `#E2E8F0` | `#FFFFFF` | — |
| Focus | `#2563EB` | `#FFFFFF` | Ring azul 3px opacity 12% |
| Error | `#EF4444` | `#FFFFFF` | Texto error `#EF4444`, texto-xs |
| Éxito | `#16A34A` | `#FFFFFF` | Texto confirmación `#16A34A` |

### 8.2 Fortaleza de contraseña (RegistroForm)

```
3 barras horizontales (h-1, flex-1, rounded-full)
Nivel 1 (≤5 chars):  Rojo #EF4444
Nivel 2 (6-9 chars, sin mayúscula o número): Amarillo #F59E0B
Nivel 3 (≥10 chars, con mayúscula y número): Verde #16A34A
Inactivo: #E2E8F0

Labels: "Débil" / "Regular" / "Fuerte"
```

---

## 9. Estados de Carga y Skeleton

### 9.1 Spinner (AuthGuard, botones loading)

```tsx
// Spinner circular
className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"

// Spinner grande (AuthGuard)
className="w-12 h-12 border-4 border-[#2563EB]/25 border-t-[#2563EB] rounded-full animate-spin"
```

### 9.2 Skeleton (CopilotSidebar)

```tsx
// Patrón animate-pulse con placeholders
className="animate-pulse rounded-xl bg-white border border-[#E2E8F0] p-4 space-y-2"
// Dentro: divs con h-3/h-4, rounded, bg-[#E2E8F0], widths variados (w-24, w-full, w-4/5, w-3/5)
```

### 9.3 Typing indicator (Chat)

```tsx
// 3 dots bounce
className="w-1.5 h-1.5 rounded-full bg-[#64748B] animate-bounce"
// Con animationDelay escalonado: 0ms, 150ms, 300ms
```

---

## 10. Íconos y Emojis (convención)

El proyecto usa emojis como sistema de iconografía (sin librería externa). Convenciones:

| Emoji | Contexto |
|-------|---------|
| 🏠 | Dashboard |
| 📋 | Diagnóstico |
| 🏢 | Empresa / Onboarding |
| ⚖️ | Legal / SIC |
| 📜 | Ley / Artículo legal |
| 🤖 | IA / Copilot |
| ✅ | Conforme / éxito |
| ⚠️ | Advertencia / riesgo |
| 🚨 | Crítico |
| 📊 | Métricas |
| 📈 | Score promedio |
| 🗂️ | Historial |
| 🔒 | Auth / seguridad |
| 🔍 | Auditor |
| 🛡️ | Admin |
| 📊 Eval | Evaluador |
| 💡 | Consejo / tip |
| 🚀 | Acción principal (CTA) |
| ➡️ / → | Acción de continuar |
| ✕ | Cerrar / cancelar |
| ✓ | Confirmar / Sí |
| 🚪 | Cerrar sesión |
| 👁️ / 🙈 | Toggle password |

---

## 11. Accesibilidad

- `label` siempre asociado via `htmlFor` + `id` en input
- `aria-label` en SVG del gauge
- `aria-hidden="true"` en backdrop del CopilotSidebar
- `role="img"` en componentes decorativos SVG
- Estados focus visibles: `focus-visible` con outline 2px azul
- `disabled` states con `disabled:opacity-50 disabled:cursor-not-allowed`
- `lang="es"` en `<html>`
- Colores con contraste WCAG (textos oscuros sobre fondos claros)

---

## 12. Convenciones Blade/Laravel para Migración

### 12.1 Mapeo Tailwind → clases utilitarias equivalentes

La paleta usa valores hex hardcodeados como `text-[#0F172A]` y `bg-[#F8FAFC]`. En Laravel con Tailwind, puedes:

1. **Extender tailwind.config.js:**

```js
// tailwind.config.js
theme: {
  extend: {
    colors: {
      'datacheck': {
        bg:      '#F8FAFC',
        surface: '#FFFFFF',
        muted:   '#F1F5F9',
        border:  '#E2E8F0',
        primary: '#2563EB',
        'primary-hover': '#1D4ED8',
        'primary-light': '#EFF6FF',
        navy:    '#041C4A',
        'navy-light': '#0A2E73',
        text:    '#0F172A',
        'text-sec': '#64748B',
        'text-muted': '#94A3B8',
        conforme: '#16A34A',
        'conforme-bg': '#DCFCE7',
        'conforme-border': '#BBF7D0',
        proceso: '#F59E0B',
        'proceso-bg': '#FEF3C7',
        'proceso-border': '#FDE68A',
        critico: '#EF4444',
        'critico-bg': '#FEE2E2',
        'critico-border': '#FECACA',
      }
    }
  }
}
```

2. **Uso resultante:**

```blade
<!-- Antes: -->
<div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl">

<!-- Después: -->
<div class="bg-datacheck-bg border border-datacheck-border rounded-2xl">
```

### 12.2 Componentes Blade sugeridos

| Componente React | Componente Blade sugerido |
|------------------|---------------------------|
| `PreguntaCard` | `resources/views/components/diagnostico/pregunta-card.blade.php` |
| `MetricCard` | `resources/views/components/shared/metric-card.blade.php` |
| `Sidebar` | `resources/views/layouts/sidebar.blade.php` |
| `ProgressBar` | `resources/views/components/diagnostico/progress-bar.blade.php` |
| `GaugeChart` | `resources/views/components/diagnostico/gauge-chart.blade.php` (SVG puro) |
| `CopilotSidebar` | `resources/views/components/diagnostico/copilot-sidebar.blade.php` |
| `SkipNotice` | `resources/views/components/diagnostico/skip-notice.blade.php` |
| `LoginForm` | `resources/views/components/auth/login-form.blade.php` |
| `RegistroForm` | `resources/views/components/auth/registro-form.blade.php` |

### 12.3 Estructura Blade recomendada

```
resources/views/
├── layouts/
│   ├── app.blade.php          # Layout autenticado (sidebar + content)
│   └── guest.blade.php        # Layout público (split-screen)
├── components/
│   ├── shared/
│   │   ├── metric-card.blade.php
│   │   └── sidebar.blade.php
│   ├── auth/
│   │   ├── login-form.blade.php
│   │   └── registro-form.blade.php
│   └── diagnostico/
│       ├── pregunta-card.blade.php
│       ├── progress-bar.blade.php
│       ├── gauge-chart.blade.php
│       ├── resultado-panel.blade.php
│       ├── copilot-sidebar.blade.php
│       └── skip-notice.blade.php
├── pages/
│   ├── dashboard.blade.php
│   ├── diagnostico.blade.php
│   ├── onboarding.blade.php
│   ├── login.blade.php
│   └── registro.blade.php
```

### 12.4 Migración de animaciones

```css
/* En resources/css/app.css (o donde cargues estilos globales) */
@keyframes fade-slide-up { ... }
@keyframes shimmer { ... }
@keyframes scale-in { ... }
@keyframes shrink-width { ... }

.animate-fade-slide-up { animation: fade-slide-up 0.45s cubic-bezier(0.16, 1, 0.3, 1) both; }
.animate-shimmer { ... }
.animate-scale-in { ... }
```

### 12.5 Migración de variables CSS a Blade

```blade
{{-- En tu layout principal, inyectar variables CSS --}}
<style>
  :root {
    --color-bg-base: #F8FAFC;
    --color-bg-surface: #FFFFFF;
    --color-border: #E2E8F0;
    --color-primary: #2563EB;
    /* ... resto de variables ... */
  }
</style>
```

---

## 13. Notas Técnicas de Migración

1. **Google Fonts Inter** — Cargar vía `@import` en CSS o `<link>` en layout Blade
2. **SVG GaugeChart** — El gauge es SVG puro, se puede copiar directamente a Blade como inline SVG
3. **Google OAuth** — Si usas Laravel Socialite, mantener el mismo flujo OAuth
4. **Backend compartido** — La API de `/api/copilot` debe replicarse como ruta Laravel
5. **Estado del diagnóstico** — En Laravel, usar Livewire, Inertia.js + Vue, o HTMX para reemplazar el estado de React
6. **CopilotSidebar** — Considerar un componente Livewire o un modal con Alpine.js para el chat
7. **Responsive** — Todos los breakpoints usan Tailwind estándar: `sm:` (640px), `md:` (768px), `lg:` (1024px)
8. **Print styles** — Replicar el `@media print` de globals.css en CSS de Laravel

---

## 14. Elementos Reutilizables para Consolidar

### ALTA prioridad (usar en ambos proyectos):

| Elemento | Razón |
|----------|-------|
| Paleta completa CAVALTEC | Ya funciona bien, no reinventar |
| Sidebar layout | Misma navegación en ambos proyectos |
| Card base pattern | Recurrente en todas las vistas |
| Input base pattern | Validación visual consistente |
| Badge de riesgo | Semántica clara BLoque/Nivel Riesgo |
| Botón primario + outline | Acciones principales y secundarias |
| Tabla de historial | Pattern de lista con hover states |

### MEDIA prioridad:

| Elemento | Razón |
|----------|-------|
| GaugeChart SVG | Puede reemplazarse por chart.js en Blade |
| CopilotSidebar | Depende de cómo se implemente IA en Laravel |
| ProgressBar shimmer | Necesita animación CSS migrada |
| SkipNotice modal | Depende de lógica de diagnóstico en Laravel |

### BAJA prioridad:

| Elemento | Razón |
|----------|-------|
| Animaciones stagger | Nice-to-have, no crítico |
| Skeleton loading | Sustituible con spinners simples |
| Patrones de swipe/float | Decorativos |

---

*Generado a partir del análisis completo de `app/globals.css`, `app/layout.tsx`, todos los componentes `app/ui/**/*.tsx` y páginas `app/**/page.tsx`.*
