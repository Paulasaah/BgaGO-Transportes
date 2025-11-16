{{-- Contenedor de notificaciones --}}
<div 
    x-data="notificationManager()" 
    @show-notification.window="showNotification($event.detail)"
    class="fixed top-4 right-4 z-50 space-y-2"
    style="max-width: 400px;">
    
    <template x-for="notification in notifications" :key="notification.id">
        <div 
            x-show="notification.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full"
            class="flex items-start gap-3 p-4 rounded-lg shadow-lg border"
            :class="{
                'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800': notification.type === 'success',
                'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800': notification.type === 'info',
                'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800': notification.type === 'warning',
                'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800': notification.type === 'error'
            }">
            
            {{-- Icono --}}
            <div class="flex-shrink-0">
                <template x-if="notification.type === 'success'">
                    <svg class="size-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </template>
                <template x-if="notification.type === 'info'">
                    <svg class="size-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </template>
                <template x-if="notification.type === 'warning'">
                    <svg class="size-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </template>
                <template x-if="notification.type === 'error'">
                    <svg class="size-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </template>
            </div>

            {{-- Contenido --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium"
                   :class="{
                       'text-green-900 dark:text-green-100': notification.type === 'success',
                       'text-blue-900 dark:text-blue-100': notification.type === 'info',
                       'text-yellow-900 dark:text-yellow-100': notification.type === 'warning',
                       'text-red-900 dark:text-red-100': notification.type === 'error'
                   }"
                   x-text="notification.message">
                </p>
            </div>

            {{-- Botón cerrar --}}
            <button 
                @click="removeNotification(notification.id)"
                class="flex-shrink-0 p-1 rounded-lg hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                <svg class="size-4 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>

<script>
function notificationManager() {
    return {
        notifications: [],
        nextId: 1,

        showNotification(detail) {
            const notification = {
                id: this.nextId++,
                message: detail.message || 'Notificación',
                type: detail.type || 'info',
                visible: true
            };

            this.notifications.push(notification);

            // Auto-remover después de 5 segundos
            setTimeout(() => {
                this.removeNotification(notification.id);
            }, 5000);
        },

        removeNotification(id) {
            const notification = this.notifications.find(n => n.id === id);
            if (notification) {
                notification.visible = false;
                // Remover del array después de la animación
                setTimeout(() => {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                }, 200);
            }
        }
    }
}
</script>
