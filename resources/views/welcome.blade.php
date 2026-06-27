<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CumplIA — Autodiagnóstico Ley 1581</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-primary { background-color: #2563eb; }
        .text-primary { color: #2563eb; }
        .text-high-text { color: #dc2626; }
        .bg-high-bg { background-color: #fef2f2; }
        .text-high-text { color: #dc2626; }
        .bg-sidebar-dark { background-color: #1e293b; }
        .text-sidebar-dark { color: #1e293b; }
    </style>
</head>
<body class="font-sans bg-gray-50">
    <nav class="bg-sidebar-dark text-white py-4">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <div class="font-bold text-xl">CumplIA</div>
            <div class="flex gap-4">
                <a href="{{ route('login') }}" class="hover:text-gray-300">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="bg-primary text-white px-4 py-2 rounded-lg">Registrarse</a>
            </div>
        </div>
    </nav>

    <section class="py-20 text-center">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-sidebar-dark mb-6">
            De la incertidumbre legal<br>
            <span class="text-high-text">a un plan de acción</span><br>
            en minutos
        </h1>
        <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
            CumplIA es la herramienta gratuita de autodiagnóstico que ayuda a su PYME 
            a evaluar su cumplimiento de la Ley 1581 de 2012.
        </p>
        <a href="{{ route('register') }}" class="px-8 py-4 bg-sidebar-dark text-white text-lg font-semibold rounded-xl inline-block">
            Comenzar autodiagnóstico
        </a>
    </section>

    <footer class="bg-sidebar-dark text-white/50 py-8 text-center">
        <p>Autodiagnóstico de cumplimiento — Ley 1581 de 2012</p>
    </footer>
</body>
</html>