{{-- resources/views/components/layouts/public.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
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
