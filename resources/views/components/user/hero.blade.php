@php
    $u = auth()->user();
    $name = trim(($u?->name ?? $u?->email) ?? '');
    $parts = preg_split('/\s+/', $name);
    $first = $parts[0] ?? (str_contains($name, '@') ? explode('@', $name)[0] : 'Usuario');
    $image = $image ?? asset('images/login/ciudad.jpg');
@endphp

<div class="pt-12 sm:pt-16">
    <div class="relative w-full rounded-2xl overflow-hidden ring-1 ring-zinc-200 dark:ring-zinc-800">
        <img src="{{ $image }}" alt="Hero" class="w-full h-[240px] sm:h-[320px] lg:h-[420px] object-cover" />
        <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent"></div>
        <div class="absolute inset-0 flex items-center">
            <div class="px-6 sm:px-10">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white">Bienvenido de vuelta, {{ $first }}</h2>
            </div>
        </div>
    </div>
</div>