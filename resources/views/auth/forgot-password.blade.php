<x-guest-layout>
<div class="text-center mb-6">
    <div class="mx-auto w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4">
        <x-icon name="lock-closed" class="w-6 h-6 text-primary" />
    </div>
    <h2 class="text-xl font-bold text-body-text">¿Olvidó su contraseña?</h2>
    <p class="text-sm text-muted-text mt-2 leading-relaxed">
        No hay problema. Ingrese su correo electrónico y le enviaremos un enlace para restablecerla.
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
            placeholder="correo@empresa.com" />
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
