@props([
    'href',
    'icon',
    'label',
])

<a 
    href="{{ $href }}" 
    wire:navigate
    {{ $attributes->merge(['class' => 'group flex flex-col items-center justify-center h-24 p-4 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:shadow-lg hover:border-blue-500 dark:hover:border-blue-500 transition-all']) }}
>
    <flux:icon :name="$icon" class="w-8 h-8 mb-2 text-zinc-600 dark:text-zinc-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" />
    <span class="text-sm font-medium text-zinc-900 dark:text-white text-center">{{ $label }}</span>
</a>