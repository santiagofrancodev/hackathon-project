<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CumplIA — Autodiagnóstico de cumplimiento de la Ley 1581 de Protección de Datos para PYMEs colombianas.">

    <title>CumplIA — Autodiagnóstico Ley 1581</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/cumplia-icon.svg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-bg-page text-body-text">
    <div class="min-h-screen flex flex-col">
        {{-- NAV --}}
<nav class="bg-sidebar-dark text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-2">
                        <x-application-logo class="h-8 w-auto" />
                    </div>
                    <div class="flex items-center gap-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-white/80 hover:text-white transition">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium text-white/80 hover:text-white transition">Iniciar sesión</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-hover transition shadow-sm">
                                        Registrarse
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        {{-- HERO --}}
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-sidebar-dark/5 via-transparent to-high-text/5 pointer-events-none"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
                <div class="max-w-3xl mx-auto text-center">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-high-bg text-high-text text-sm font-medium rounded-full mb-6">
                        <span class="w-2 h-2 bg-high-text rounded-full animate-pulse"></span>
                        Protección de Datos Personales
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-sidebar-dark leading-tight mb-6">
                        De la incertidumbre legal<br>
                        <span class="text-high-text">a un plan de acción</span><br>
                        en minutos
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
CumplIA es la herramienta gratuita de autodiagnóstico que ayuda a su PYME 
                         a evaluar su cumplimiento de la <strong>Ley 1581 de 2012</strong> con la ayuda de 
                         inteligencia artificial — sin necesitar un abogado de planta.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('register') }}" 
                           class="px-8 py-4 bg-sidebar-dark text-white text-lg font-semibold rounded-xl hover:bg-sidebar-dark/90 transition shadow-lg hover:shadow-xl flex items-center gap-2">
                            Comenzar autodiagnóstico
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                        </a>
                        <a href="#features" class="px-8 py-4 border-2 border-border-light text-muted-text text-lg font-semibold rounded-xl hover:border-gray-300 transition">
                            Conocer más
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- FEATURES --}}
        <section id="features" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-sidebar-dark mb-4">¿Por qué CumplIA?</h2>
                    <p class="text-lg text-muted-text max-w-2xl mx-auto">
                        La Ley 1581 de 2012 es de obligatorio cumplimiento. La SIC sanciona con multas de hasta 
                        <strong>2.000 SMMLV</strong>. Hoy hay dos opciones: contratar una consultoría costosa, o no hacer nada. 
                        <strong>CumplIA es la tercera opción.</strong>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {{-- Feature 1 --}}
                    <div class="bg-bg-page rounded-2xl p-8 hover:shadow-lg transition border border-border-light">
                        <div class="w-14 h-14 bg-high-bg rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-high-text" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="m9 14 2 2 4-4"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-sidebar-dark mb-3">Diagnóstico Inteligente</h3>
                        <p class="text-muted-text leading-relaxed">
                            11 preguntas estructuradas en 3 bloques clave: política de datos, privacidad desde el diseño 
                            y gobernanza. Responde en menos de 10 minutos y obtén tu nivel de cumplimiento.
                        </p>
                    </div>

                    {{-- Feature 2 --}}
                    <div class="bg-bg-page rounded-2xl p-8 hover:shadow-lg transition border border-border-light">
                        <div class="w-14 h-14 bg-high-bg rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-sidebar-dark mb-3">Cumplimiento Ley 1581</h3>
                        <p class="text-muted-text leading-relaxed">
                            Cada pregunta está alineada con la regulación colombiana de protección de datos. 
                            El cálculo de puntaje refleja exactamente los pesos definidos por el marco normativo.
                        </p>
                    </div>

                    {{-- Feature 3 --}}
                    <div class="bg-bg-page rounded-2xl p-8 hover:shadow-lg transition border border-border-light">
                        <div class="w-14 h-14 bg-medium-bg rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-medium-text" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-sidebar-dark mb-3">Recomendaciones con IA</h3>
                        <p class="text-muted-text leading-relaxed">
                            No solo sabrás tu porcentaje de cumplimiento. Recibirás un plan de acción priorizado 
                            con recomendaciones prácticas para cerrar cada brecha identificada.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- HOW IT WORKS --}}
        <section class="py-20 bg-bg-page">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-sidebar-dark mb-4">Cómo funciona</h2>
                    <p class="text-lg text-muted-text">Tres pasos simples para conocer tu nivel de cumplimiento</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-sidebar-dark text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold">1</div>
                        <h3 class="text-lg font-bold text-sidebar-dark mb-2">Regístrate</h3>
                        <p class="text-muted-text">Crea tu cuenta y registra los datos de tu empresa en menos de 2 minutos.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-sidebar-dark text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold">2</div>
                        <h3 class="text-lg font-bold text-sidebar-dark mb-2">Responde</h3>
                        <p class="text-muted-text">Completa el cuestionario de 11 preguntas sobre tus prácticas de protección de datos.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-sidebar-dark text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold">3</div>
                        <h3 class="text-lg font-bold text-sidebar-dark mb-2">Actúa</h3>
                        <p class="text-muted-text">Recibe tu resultado, identifica brechas y sigue el plan de acción recomendado.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA FINAL --}}
        <section class="py-20 bg-sidebar-dark">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                    ¿Tu PYME cumple con la Ley 1581?
                </h2>
                <p class="text-lg text-white/70 mb-8">
                    La Superintendencia de Industria y Comercio sanciona el incumplimiento. 
                    Entérate hoy mismo sin costo y sin compromiso.
                </p>
                <a href="{{ route('register') }}" 
                   class="inline-flex items-center px-8 py-4 bg-primary text-white text-lg font-semibold rounded-xl hover:bg-primary-hover transition shadow-lg">
                    Iniciar autodiagnóstico gratis
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                </a>
            </div>
        </section>

        {{-- FOOTER --}}
        <footer class="bg-sidebar-dark border-t border-white/10 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <x-application-logo class="h-8 w-auto" />
                    </div>
                    <p class="text-white/50 text-sm">
                        Autodiagnóstico de cumplimiento — Ley 1581 de 2012
                    </p>
                    <p class="text-white/30 text-xs">
                        Hackathon Cavaltec / Talento Tech
                    </p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
