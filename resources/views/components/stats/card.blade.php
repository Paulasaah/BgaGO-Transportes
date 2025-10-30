{{-- resources/views/components/stats/card.blade.php --}}
@props([
    'title',
    'value',
    'change' => null,
    'changeType' => 'positive', // positive, negative, neutral
    'icon' => null,             // icon name (string) -> check, truck, user-plus, etc.
    'color' => 'blue',          // blue, green, purple, amber, red, yellow
])

@php
    // Paleta de colores para íconos
    $colorClasses = [
        'blue' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
        'green' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
        'purple' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
        'amber' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400',
        'red' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
        'yellow' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400', // ✅ agregado
    ];

    // Colores para el texto de cambio (+/-)
    $changeColors = [
        'positive' => 'text-green-600 dark:text-green-400',
        'negative' => 'text-red-600 dark:text-red-400',
        'neutral'  => 'text-zinc-600 dark:text-zinc-400',
    ];

    // Fallback seguro (por si pasan un color no definido)
    $colorClass = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $title }}</p>
            <p class="text-2xl font-semibold mt-1">{{ $value }}</p>
            @if($change)
                <p class="text-sm mt-1 {{ $changeColors[$changeType] ?? '' }}">{{ $change }}</p>
            @endif
        </div>

        @if($icon)
            {{-- 🔹 Caja de ícono con color dinámico --}}
            <div class="h-12 w-12 rounded-lg flex items-center justify-center {{ $colorClass }}">
                @switch($icon)
                    @case('users')
                        {{-- 👥 Users --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2h5m6-10a4 4 0 100-8 4 4 0 000 8z" />
                        </svg>
                        @break

                    @case('truck')
                        {{-- 🚚 Truck --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13V6a1 1 0 011-1h9v8H3zm9 5a3 3 0 11-6 0 3 3 0 016 0zM13 13h6l3 3v2a1 1 0 01-1 1h-1a3 3 0 11-6 0h-1v-6z" />
                        </svg>
                        @break

                    @case('user-plus')
                        {{-- ➕ User Plus --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m12-8a4 4 0 100-8 4 4 0 000 8zm6 8v-6m-3 3h6" />
                        </svg>
                        @break

                    @case('check-circle')
                        {{-- ✅ Check Circle --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4m5 2a9 9 0 11-18 0a9 9 0 0118 0z" />
                        </svg>
                        @break

                    @case('x-circle')
                        {{-- ❌ X Circle --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2l2 2m0-4l-2 2l-2-2m5 2a9 9 0 11-18 0a9 9 0 0118 0z" />
                        </svg>
                        @break

                    @default
                        {{-- 📊 Default chart icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v18h18M9 17V9m4 8v-4m4 4V5" />
                        </svg>
                @endswitch
            </div>
        @endif
    </div>
</div>
