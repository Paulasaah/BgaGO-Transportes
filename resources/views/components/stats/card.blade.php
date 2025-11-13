{{-- resources/views/components/stats/card.blade.php --}}

@props(['title', 'value', 'icon', 'color' => 'blue'])

@php
    $colorClasses = [
        'blue' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
        'green' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
        'purple' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
        'red' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
    ];
@endphp

<div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">
                {{ $title }}
            </p>
            <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                {{ $value }}
            </p>
        </div>
        <div class="flex h-12 w-12 items-center justify-center rounded-lg {{ $colorClasses[$color] ?? $colorClasses['blue'] }}">
            @if($icon === 'currency-dollar')
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                    <path d="M12 1v22" />
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                </svg>
            @elseif($icon === 'cube')
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                    <path d="M3.3 7L12 12l8.7-5" />
                    <path d="M7 17.5L12 15l5 2.5" />
                </svg>
            @else
                <i data-lucide="{{ $icon }}" class="h-6 w-6"></i>
            @endif
        </div>
    </div>
</div>