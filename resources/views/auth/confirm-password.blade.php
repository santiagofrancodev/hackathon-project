<x-guest-layout>
<div class="text-center mb-6">
    <div class="mx-auto w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4">
        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
    </div>
    <h2 class="text-xl font-bold text-body-text">Confirmar contraseña</h2>
    <p class="text-sm text-muted-text mt-2 leading-relaxed">
        Esta es un área segura de la aplicación. Por favor, confirma tu contraseña antes de continuar.
    </p>
</div>

<form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
    @csrf

    <div>
        <x-input-label for="password" :value="__('Contraseña')" />
        <x-text-input id="password" class="block mt-1.5 w-full" type="password"
            name="password" required autocomplete="current-password"
            placeholder="Ingresa tu contraseña" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <button type="submit"
            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition">
        Confirmar
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
    </button>
</form>
</x-guest-layout>
