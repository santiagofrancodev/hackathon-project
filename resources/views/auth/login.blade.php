<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Iniciar sesión — CheckData AI</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('img/cumplia-icon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen grid grid-cols-1 md:grid-cols-2">

        {{-- LEFT PANEL — Welcome / Branding --}}
        <div class="relative flex flex-col justify-between bg-[#0A0F24] text-white p-8 lg:p-12 min-h-[50vh] md:min-h-screen overflow-hidden">

            {{-- Background decorative elements --}}
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 w-60 h-60 bg-blue-600/5 rounded-full blur-3xl"></div>
                <div class="absolute top-1/4 right-1/4 w-32 h-32 bg-blue-400/5 rounded-full blur-2xl"></div>
                {{-- Subtle grid pattern --}}
                <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 25px 25px, white 1px, transparent 1px); background-size: 50px 50px;"></div>
            </div>

            {{-- Content --}}
            <div class="relative z-10 flex flex-col flex-1">

                {{-- Logo --}}
                <div class="mb-12 md:mb-16">
                    <x-application-logo class="h-9 lg:h-10 w-auto" />
                </div>

                {{-- Hero text --}}
                <div class="flex-1 flex flex-col justify-center max-w-lg">
                    <h1 class="text-3xl lg:text-4xl xl:text-5xl font-extrabold leading-tight mb-4">
                        Protege los datos
                        <br><span class="text-blue-400">con el poder de la IA</span>
                    </h1>
                    <p class="text-base lg:text-lg text-gray-400 leading-relaxed max-w-md">
                        Autodiagnóstico inteligente de cumplimiento de la <strong class="text-white">Ley 1581 de 2012</strong> 
                        para PYMEs colombianas. Sin abogados, sin complicaciones, en minutos.
                    </p>

                    {{-- Benefits --}}
                    <div class="mt-10 space-y-5">
                        <div class="flex items-start gap-4">
                            <div class="w-9 h-9 bg-blue-500/15 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                                <x-icon name="shield-check" class="w-5 h-5 text-blue-400" />
                            </div>
                            <div>
                                <p class="font-semibold text-white">Diagnóstico preciso</p>
                                <p class="text-sm text-gray-400 mt-0.5">Evaluación estructurada en 3 bloques clave con pesos normativos exactos</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-9 h-9 bg-blue-500/15 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                                <x-icon name="bolt" class="w-5 h-5 text-blue-400" />
                            </div>
                            <div>
                                <p class="font-semibold text-white">Recomendaciones con IA</p>
                                <p class="text-sm text-gray-400 mt-0.5">Plan de acción priorizado para cerrar cada brecha de cumplimiento</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-9 h-9 bg-blue-500/15 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                                <x-icon name="lock-closed" class="w-5 h-5 text-blue-400" />
                            </div>
                            <div>
                                <p class="font-semibold text-white">Cumplimiento normativo</p>
                                <p class="text-sm text-gray-400 mt-0.5">Alineado con la Ley 1581 y los estándares de la Superintendencia de Industria y Comercio</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="relative z-10 pt-8 border-t border-white/5">
                <p class="text-xs text-gray-500">
                    Hackathon Cavaltec / Talento Tech &mdash; Protección de Datos Personales
                </p>
            </div>
        </div>

        {{-- RIGHT PANEL — Login form --}}
        <div class="flex items-center justify-center p-8 lg:p-12 bg-card-bg min-h-screen">
            <div class="w-full max-w-sm">

                {{-- Mobile-only logo --}}
                <div class="md:hidden flex justify-center mb-8">
                    <x-application-logo class="h-9 w-auto text-body-text" />
                </div>

                {{-- Header --}}
                <div class="mb-8 text-center md:text-left">
                    <h2 class="text-2xl font-bold text-body-text">Iniciar sesión</h2>
                    <p class="text-sm text-muted-text mt-1">Ingrese sus credenciales para continuar</p>
                </div>

                {{-- Session Status --}}
                <x-auth-session-status class="mb-4" :status="session('status')" />

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-low-bg border border-low-text/20 text-low-text text-sm rounded-lg">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Google OAuth Button --}}
                <a href="{{ route('auth.google') }}"
                   class="w-full flex items-center justify-center gap-3 px-4 py-2.5 border border-border-light rounded-lg hover:bg-bg-page transition text-sm font-medium text-body-text mb-4">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Continuar con Google
                </a>

                {{-- Divider --}}
                <div class="relative mb-4">
                    <div class="absolute inset-0 flex items-center"><span class="w-full border-t border-border-light"></span></div>
                    <div class="relative flex justify-center text-xs"><span class="bg-card-bg px-2 text-muted-text">o continuar con correo</span></div>
                </div>

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <x-input-label for="email" :value="__('Correo electrónico')" />
                        <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email"
                            :value="old('email')" required autofocus autocomplete="username"
                            placeholder="correo@empresa.com" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label for="password" :value="__('Contraseña')" />
                            @if (Route::has('password.request'))
                                <a class="text-xs text-primary hover:text-primary-hover font-medium"
                                   href="{{ route('password.request') }}">
                                    ¿Olvidó su contraseña?
                                </a>
                            @endif
                        </div>
                        <x-text-input id="password" class="block mt-1.5 w-full" type="password"
                            name="password" required autocomplete="current-password"
                            placeholder="••••••••" />
                    </div>

                    {{-- Remember --}}
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox"
                               class="rounded border-border-light text-primary shadow-sm focus:ring-primary"
                               name="remember">
                        <label for="remember_me" class="ms-2 text-sm text-muted-text">
                            Recordarme
                        </label>
                    </div>

                    {{-- Submit --}}
<button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition">
                        Iniciar sesión
                        <x-icon name="arrow-right" class="w-4 h-4" />
                    </button>

                    {{-- Register link --}}
                    <p class="text-center text-sm text-muted-text pt-2">
                        ¿No tiene cuenta?
                        <a href="{{ route('register') }}" class="text-primary hover:text-primary-hover font-semibold">
                            Regístrese aquí
                        </a>
                    </p>
                </form>
            </div>
        </div>

    </div>
</body>
</html>
