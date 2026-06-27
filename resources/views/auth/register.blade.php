<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Crear cuenta — CheckData AI</title>

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
                        Comience su diagnóstico
                        <br><span class="text-blue-400">en menos de 2 minutos</span>
                    </h1>
                    <p class="text-base lg:text-lg text-gray-400 leading-relaxed max-w-md">
                        Regístrese gratis y descubra el nivel de cumplimiento de su empresa 
                        con la <strong class="text-white">Ley 1581 de Protección de Datos</strong> — sin costo, sin compromiso.
                    </p>

                    {{-- Benefits --}}
                    <div class="mt-10 space-y-5">
                        <div class="flex items-start gap-4">
                            <div class="w-9 h-9 bg-blue-500/15 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                                <x-icon name="clock" class="w-5 h-5 text-blue-400" />
                            </div>
                            <div>
                                <p class="font-semibold text-white">Resultados inmediatos</p>
                                <p class="text-sm text-gray-400 mt-0.5">Complete 11 preguntas y obtenga tu porcentaje de cumplimiento al instante</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-9 h-9 bg-blue-500/15 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                                <x-icon name="lock-closed"-check class="w-5 h-5 text-blue-400" />
                            </div>
                            <div>
                                <p class="font-semibold text-white">Sin riesgo legal</p>
                                <p class="text-sm text-gray-400 mt-0.5">Identifica brechas antes de una fiscalización de la Superintendencia de Industria y Comercio</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-9 h-9 bg-blue-500/15 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                                <x-icon name="users" class="w-5 h-5 text-blue-400" />
                            </div>
                            <div>
                                <p class="font-semibold text-white">Diseñado para PYMEs</p>
                                <p class="text-sm text-gray-400 mt-0.5">Sin jerga legal compleja. Lenguaje claro, pensado para dueños y emprendedores</p>
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

        {{-- RIGHT PANEL — Register form --}}
        <div class="flex items-center justify-center p-8 lg:p-12 bg-card-bg min-h-screen">
            <div class="w-full max-w-sm">

                {{-- Mobile-only logo --}}
                <div class="md:hidden flex justify-center mb-8">
                    <x-application-logo class="h-9 w-auto text-body-text" />
                </div>

                {{-- Header --}}
                <div class="mb-8 text-center md:text-left">
                    <h2 class="text-2xl font-bold text-body-text">Crear cuenta</h2>
                    <p class="text-sm text-muted-text mt-1">Complete sus datos para registrarse</p>
                </div>

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

                {{-- Form --}}
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <x-input-label for="name" :value="__('Nombre completo')" />
                        <x-text-input id="name" class="block mt-1.5 w-full" type="text" name="name"
                            :value="old('name')" required autofocus autocomplete="name"
                            placeholder="Nombre completo" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-input-label for="email" :value="__('Correo electrónico')" />
                        <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email"
                            :value="old('email')" required autocomplete="username"
                            placeholder="correo@empresa.com" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <x-input-label for="password" :value="__('Contraseña')" />
                        <x-text-input id="password" class="block mt-1.5 w-full" type="password"
                            name="password" required autocomplete="new-password"
                            placeholder="Mínimo 8 caracteres" />
                        <p class="mt-1 text-xs text-muted-text">Mínimo 8 caracteres</p>
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
                        <x-text-input id="password_confirmation" class="block mt-1.5 w-full" type="password"
                            name="password_confirmation" required autocomplete="new-password"
                            placeholder="Repita su contraseña" />
                    </div>

                    {{-- Submit --}}
<button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition mt-6">
                            Crear cuenta
                            <x-icon name="arrow-right" class="w-4 h-4" />
                        </button>

                    {{-- Login link --}}
                    <p class="text-center text-sm text-muted-text pt-2">
                        ¿Ya tiene cuenta?
                        <a href="{{ route('login') }}" class="text-primary hover:text-primary-hover font-semibold">
                            Inicia sesión
                        </a>
                    </p>
                </form>
            </div>
        </div>

    </div>
</body>
</html>
