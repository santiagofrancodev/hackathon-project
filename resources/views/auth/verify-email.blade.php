<x-guest-layout>
<div class="text-center mb-6">
    <div class="mx-auto w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4">
        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
        </svg>
    </div>
    <h2 class="text-xl font-bold text-body-text">Verifica tu correo</h2>
    <p class="text-sm text-muted-text mt-2 leading-relaxed">
        Gracias por registrarte. Antes de empezar, necesitamos que verifiques tu dirección de correo electrónico.
        Te enviamos un enlace de verificación. Si no lo recibiste, te podemos enviar otro.
    </p>
</div>

@if (session('status') == 'verification-link-sent')
    <div class="mb-4 p-3 bg-high-bg border border-high-text/20 text-high-text text-sm rounded-lg">
        Se ha enviado un nuevo enlace de verificación al correo que proporcionaste durante el registro.
    </div>
@endif

<div class="flex flex-col sm:flex-row items-center justify-between gap-4">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit"
                class="w-full sm:w-auto px-5 py-2.5 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition">
            Reenviar verificación
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="text-sm text-muted-text hover:text-body-text font-medium transition">
            Cerrar sesión
        </button>
    </form>
</div>
</x-guest-layout>
