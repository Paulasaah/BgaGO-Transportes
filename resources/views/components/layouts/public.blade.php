{{-- resources/views/components/layouts/public.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <script>(function(){var t=localStorage.getItem('theme');if(!t){t='light';localStorage.setItem('theme',t);}document.documentElement.classList.toggle('dark',t==='dark');document.documentElement.setAttribute('data-theme',t);})();</script>
    @include('partials.head')
    @livewireStyles
</head>
<body class="min-h-screen bg-white dark:bg-zinc-900 antialiased">
    {{-- Navbar global --}}
    @include('components.navbar')

    <main class="pt-20">
        {{ $slot }}
    </main>

    @include('components.footer')
    @livewireScripts
</body>
</html>
