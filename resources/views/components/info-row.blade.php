@props(['label', 'value', 'icon' => null, 'tooltip' => null])

<div class="flex items-start gap-2">
    @if($icon)
        <flux:icon :name="$icon" class="size-4 mt-1 text-zinc-500 dark:text-zinc-400" />
    @endif
    <div>
        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1 flex items-center gap-1">
            {{ $label }}
            @if($tooltip)
                <flux:tooltip text="{{ $tooltip }}" position="top" />
            @endif
        </div>
        <div class="font-semibold text-zinc-900 dark:text-zinc-100 truncate max-w-[240px]">
            {{ $value }}
        </div>
    </div>
</div>
