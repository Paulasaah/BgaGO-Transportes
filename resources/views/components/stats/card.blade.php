@props([
    'title',
    'value',
    'change' => null,
    'changeType' => 'positive', // positive, negative, neutral
    'icon' => 'chart-bar',
    'color' => 'blue', // blue, green, purple, amber, red
])

@php
$colorClasses = [
    'blue' => [
        'bg' => 'bg-blue-100 dark:bg-blue-900/30',
        'text' => 'text-blue-600 dark:text-blue-400',
    ],
    'green' => [
        'bg' => 'bg-green-100 dark:bg-green-900/30',
        'text' => 'text-green-600 dark:text-green-400',
    ],
    'purple' => [
        'bg' => 'bg-purple-100 dark:bg-purple-900/30',
        'text' => 'text-purple-600 dark:text-purple-400',
    ],
    'amber' => [
        'bg' => 'bg-amber-100 dark:bg-amber-900/30',
        'text' => 'text-amber-600 dark:text-amber-400',
    ],
    'red' => [
        'bg' => 'bg-red-100 dark:bg-red-900/30',
        'text' => 'text-red-600 dark:text-red-400',
    ],
];

$changeColor = match($changeType) {
    'positive' => 'text-green-600 dark:text-green-400',
    'negative' => 'text-red-600 dark:text-red-400',
    'neutral' => 'text-zinc-600 dark:text-zinc-400',
};
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-zinc-800 rounded-lg shadow p-6 border border-zinc-200 dark:border-zinc-700 hover:shadow-lg transition-shadow']) }}>
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $title }}</p>
            <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-2">{{ $value }}</p>
        </div>
        <div class="p-3 {{ $colorClasses[$color]['bg'] }} rounded-lg">
            <flux:icon :name="$icon" class="w-8 h-8 {{ $colorClasses[$color]['text'] }}" />
        </div>
    </div>
    
    @if($change)
    <p class="text-sm {{ $changeColor }} mt-4">
        {{ $change }}
    </p>
    @endif
</div>