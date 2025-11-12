{{--
    Componente: Landing Layout
    Propósito: Layout base para landing con diseño exacto del proyecto
    Ubicación: resources/views/layouts/landing.blade.php
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')

    <style>
        /* Evita parpadeos cuando Alpine oculta nodos */
        [x-cloak] { display: none !important; }

        /* Animaciones decorativas */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float-delayed { animation: float 6s ease-in-out 2s infinite; }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-900 antialiased">
    <!-- Navbar -->
    @include('components.navbar')

    <!-- Contenido principal -->
    @yield('content')

    <!-- Footer -->
    @include('components.footer')

    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>
