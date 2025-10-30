{{-- resources/views/components/table/actions.blade.php --}}
@props([
    'showView' => true,
    'showEdit' => true,
    'showDelete' => true,
    'onView' => null,
    'onEdit' => null,
    'onDelete' => null,
])

<div {{ $attributes->merge(['class' => 'flex justify-end gap-2']) }}>
    @if($showView)
        <flux:button 
            size="sm" 
            variant="ghost" 
            icon="eye" 
            wire:click="{{ $onView }}"
            title="Ver"
        />
    @endif

    @if($showEdit)
        <flux:button 
            size="sm" 
            variant="ghost" 
            icon="pencil" 
            wire:click="{{ $onEdit }}"
            title="Editar"
        />
    @endif

    @if($showDelete)
        <flux:button 
            size="sm" 
            variant="ghost" 
            color="red"
            icon="trash" 
            wire:click="{{ $onDelete }}"
            title="Eliminar"
        />
    @endif
</div>
