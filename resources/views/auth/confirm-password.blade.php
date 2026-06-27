<x-guest-layout>
<div class="text-center mb-6">
    <div class="mx-auto w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4">
        <x-icon name="lock-closed" class="w-6 h-6 text-primary" />
    </div>
    <h2 class="text-xl font-bold text-body-text">Confirmar contraseña</h2>
    <p class="text-sm text-muted-text mt-2 leading-relaxed">
        Esta es un área segura de la aplicación. Por favor, confirme su contraseña antes de continuar.
    </p>
</div>

<form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
    @csrf

    <div>
        <x-input-label for="password" :value="__('Contraseña')" />
        <x-text-input id="password" class="block mt-1.5 w-full" type="password"
            name="password" required autocomplete="current-password"
            placeholder="Ingrese su contraseña" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <button type="submit"
            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition">
        Confirmar
        <x-icon name="check" class="w-4 h-4" />
    </button>
</form>
</x-guest-layout>
