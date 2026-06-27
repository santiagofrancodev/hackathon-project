<x-guest-layout>
<div class="text-center mb-6">
    <div class="mx-auto w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4">
        <x-icon name="lock-closed" class="w-6 h-6 text-primary" />
    </div>
    <h2 class="text-xl font-bold text-body-text">Restablecer contraseña</h2>
    <p class="text-sm text-muted-text mt-2">Elija una contraseña nueva para su cuenta</p>
</div>

<form method="POST" action="{{ route('password.store') }}" class="space-y-5">
    @csrf

    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div>
        <x-input-label for="email" :value="__('Correo electrónico')" />
        <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email"
            :value="old('email', $request->email)" required autofocus autocomplete="username"
            placeholder="correo@empresa.com" />
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
            placeholder="Repita su contraseña" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>

    <button type="submit"
            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition">
        Restablecer contraseña
        <x-icon name="arrow-right" class="w-4 h-4" />
    </button>
</form>
</x-guest-layout>
