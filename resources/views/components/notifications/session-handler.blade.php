{{-- Manejador de notificaciones desde sesión --}}
@if(session('notification'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notification = @json(session('notification'));
            
            // Disparar evento Livewire para mostrar notificación
            Livewire.dispatch('notify', notification.message, notification.type);
        });
    </script>
@endif
