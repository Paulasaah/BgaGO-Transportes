{{-- resources/views/components/filters/bar.blade.php --}}
@props([
    'searchPlaceholder' => 'Buscar...',
    'showReset' => false,
])

<div {{ $attributes->merge([
    'class' => 'flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between'
]) }}>
    <div class="flex-1 max-w-md">
        <flux:input 
            type="text"
            placeholder="{{ $searchPlaceholder }}"
            wire:model.live="search"
            class="w-full"
        />
    </div>

    <div class="flex items-center gap-2">
        {{ $slot }}

        @if($showReset)
            <flux:button 
                variant="ghost" 
                wire:click="resetFilters" 
                icon="x-mark"
            >
                Limpiar
            </flux:button>
        @endif
    </div>
</div>
