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
            <i data-lucide="{{ $icon }}" class="h-6 w-6"></i>
        </div>
    </div>
</div>