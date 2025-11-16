<x-layouts.app.sidebar>
    <flux:main>
        {{ $slot }}
    </flux:main>
    
    {{-- Sistema de notificaciones Toast --}}
    <livewire:notifications.toast-notification />
    
    {{-- Manejador de notificaciones desde sesión --}}
    <x-notifications.session-handler />
</x-layouts.app.sidebar>
