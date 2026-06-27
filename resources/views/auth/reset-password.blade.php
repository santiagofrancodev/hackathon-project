<x-guest-layout>
<div class="text-center mb-6">
    <div class="mx-auto w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4">
        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
    </div>
    <h2 class="text-xl font-bold text-body-text">Restablecer contraseña</h2>
    <p class="text-sm text-muted-text mt-2">Elige una contraseña nueva para tu cuenta</p>
</div>

<form method="POST" action="{{ route('password.store') }}" class="space-y-5">
    @csrf

    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div>
        <x-input-label for="email" :value="__('Correo electrónico')" />
        <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email"
            :value="old('email', $request->email)" required autofocus autocomplete="username"
            placeholder="tu@empresa.com" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="password" :value="__('Nueva contraseña')" />
        <x-text-input id="password" class="block mt-1.5 w-full" type="password"
            name="password" required autocomplete="new-password"
            placeholder="Mínimo 8 caracteres" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
        <x-text-input id="password_confirmation" class="block mt-1.5 w-full" type="password"
            name="password_confirmation" required autocomplete="new-password"
            placeholder="Repite tu contraseña" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>

    <button type="submit"
            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition">
        Restablecer contraseña
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
    </button>
</form>
</x-guest-layout>
