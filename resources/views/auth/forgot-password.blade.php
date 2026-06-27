<x-guest-layout>
<div class="text-center mb-6">
    <div class="mx-auto w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4">
        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
    </div>
    <h2 class="text-xl font-bold text-body-text">¿Olvidaste tu contraseña?</h2>
    <p class="text-sm text-muted-text mt-2 leading-relaxed">
        No hay problema. Ingresa tu correo electrónico y te enviaremos un enlace para restablecerla.
    </p>
</div>

<!-- Session Status -->
<x-auth-session-status class="mb-4" :status="session('status')" />

<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
    @csrf

    <div>
        <x-input-label for="email" :value="__('Correo electrónico')" />
        <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email"
            :value="old('email')" required autofocus
            placeholder="tu@empresa.com" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div class="flex items-center justify-between">
        <a href="{{ route('login') }}" class="text-sm text-primary hover:text-primary-hover font-medium">
            &larr; Volver al inicio
        </a>
        <button type="submit"
                class="px-5 py-2.5 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition">
            Enviar enlace
        </button>
    </div>
</form>
</x-guest-layout>
