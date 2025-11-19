<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <script>(function(){var t=localStorage.getItem('theme');if(!t){t='light';localStorage.setItem('theme',t);}document.documentElement.classList.toggle('dark',t==='dark');})();</script>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-900">
        <x-navbar />

        <main class="min-h-[calc(100vh-64px)]">
            {{ $slot }}
        </main>

        <x-footer />

        @fluxScripts
        @vite(['resources/js/app.js'])
    </body>
    </html>