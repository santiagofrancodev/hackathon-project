<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

        <title>{{ config('app.name', 'CheckData AI') }}</title>

        <link rel="icon" type="image/svg+xml" href="{{ asset('img/cumplia-icon.svg') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-8 bg-[#0A0F24] overflow-hidden relative">

            {{-- Background decorative elements --}}
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 w-60 h-60 bg-blue-600/5 rounded-full blur-3xl"></div>
                <div class="absolute top-1/4 right-1/4 w-32 h-32 bg-blue-400/5 rounded-full blur-2xl"></div>
                <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 25px 25px, white 1px, transparent 1px); background-size: 50px 50px;"></div>
            </div>

            {{-- Content --}}
            <div class="relative z-10 flex flex-col items-center">
                {{-- Logo --}}
                <a href="/" class="mb-8">
                    <x-application-logo class="h-9 w-auto" />
                </a>

                {{-- Card --}}
                <div class="w-full max-w-md bg-card-bg border border-border-light rounded-2xl shadow-sm p-7">
                    {{ $slot }}
                </div>

                {{-- Footer --}}
                <p class="mt-6 text-xs text-gray-500">
                    Hackathon Cavaltec / Talento Tech
                </p>
            </div>
        </div>
    </body>
</html>
