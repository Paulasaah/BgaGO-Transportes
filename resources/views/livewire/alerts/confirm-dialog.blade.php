<?php

use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    public bool $open = false;
    public ?int $resourceId = null;

    #[On('open-confirm')]
    public function open($id): void
    {
        $this->open = true;
        $this->resourceId = (int) $id;
    }

    public function cancel(): void
    {
        $this->open = false;
        $this->resourceId = null;
    }

    public function confirm(): void
    {
        $this->dispatch('confirm-delete-ok', id: $this->resourceId);
        $this->open = false;
        $this->resourceId = null;
    }
}; ?>

<div x-data="{ open: $wire.entangle('open') }">
    <div
        x-cloak
        x-show="open"
        x-transition.opacity.duration.150ms
        class="fixed inset-0 z-[90] bg-black/40 backdrop-blur-sm"
    ></div>

    <div
        x-cloak
        x-show="open"
        x-transition.duration.150ms
        class="fixed inset-0 z-[91] flex items-center justify-center p-4"
    >
        <div class="w-full max-w-md rounded-xl border border-zinc-200 bg-white shadow-lg dark:border-zinc-800 dark:bg-zinc-900">
            <div class="px-5 pt-5">
                <flux:heading size="lg">Confirmar eliminación</flux:heading>
                <flux:subheading>¿Estás seguro de eliminar este registro?</flux:subheading>
            </div>

            <div class="px-5 pt-4 pb-5 flex justify-end gap-2">
                <flux:button variant="filled" @click="$wire.cancel()">Cancelar</flux:button>
                <flux:button variant="danger" @click="$wire.confirm()">Confirmar</flux:button>
            </div>
        </div>
    </div>
</div>