<?php

use Livewire\Volt\Component;

new class extends Component {
    // Componente de presentación: maneja solo toasts vía eventos del navegador
}; ?>

<div
    x-data="{
        toasts: [],
        addToast(e) {
            const { type, message } = e.detail || {};
            const id = Date.now() + Math.random();
            const colors = {
                success: 'border-green-200 bg-green-50 text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-400',
                error: 'border-red-200 bg-red-50 text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400',
                warning: 'border-yellow-200 bg-yellow-50 text-yellow-700 dark:border-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                info: 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            };
            this.toasts.push({ id, type, message, cls: colors[type] || colors.info, visible: true });
            setTimeout(() => this.close(id), 3500);
        },
        close(id) {
            const t = this.toasts.find(t => t.id === id);
            if (!t) return;
            t.visible = false;
            setTimeout(() => {
                this.toasts = this.toasts.filter(x => x.id !== id);
            }, 150);
        }
    }"
    x-on:notify.window="addToast($event)"
    class="fixed top-4 right-4 z-[100] space-y-2"
>
    <template x-for="t in toasts" :key="t.id">
        <div
            x-show="t.visible"
            x-transition.opacity.duration.150ms
            x-transition:enter.start="translate-y-1 opacity-0"
            x-transition:enter.end="translate-y-0 opacity-100"
            x-transition:leave.start="opacity-100"
            x-transition:leave.end="opacity-0"
            class="w-80 rounded-lg shadow-md border px-4 py-2 text-sm backdrop-blur-sm"
            :class="t.cls"
        >
            <div class="flex items-start gap-2">
                <div class="mt-0.5">
                    <flux:icon.check-circle x-show="t.type === 'success'" class="size-4" />
                    <flux:icon.exclamation-triangle x-show="t.type === 'warning'" class="size-4" />
                    <flux:icon.information-circle x-show="t.type === 'info'" class="size-4" />
                    <flux:icon.x-circle x-show="t.type === 'error'" class="size-4" />
                </div>
                <div class="flex-1" x-text="t.message"></div>
                <button type="button" class="opacity-70 hover:opacity-100" @click="close(t.id)">
                    <flux:icon.x-mark class="size-4" />
                </button>
            </div>
        </div>
    </template>
</div>