<x-layouts.app>
    <div class="flex flex-col h-screen max-h-screen overflow-hidden p-6 lg:p-8">
        <!-- Header Fijo -->
        <div class="flex-shrink-0 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="xl">Centro de Monitoreo</flux:heading>
                    <flux:subheading>Supervisión de operaciones en tiempo real</flux:subheading>
                </div>

                <!-- Controles de Actualización -->
                <div class="flex items-center gap-3">
                    <flux:button 
                        wire:click="$dispatch('refresh-monitoring')"
                        icon="arrow-path"
                        variant="primary"
                    >
                        Actualizar Todo
                    </flux:button>

                    <div class="flex items-center gap-2 px-3 py-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <div class="size-2 rounded-full bg-green-500 animate-pulse"></div>
                        <span class="text-sm text-green-700 dark:text-green-300">En vivo</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid Principal con Scroll -->
        <div class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 min-h-0 flex flex-col">
                <div class="flex-1 overflow-y-auto">
                    <livewire:monitoring.live-services />
                </div>
            </div>

            <div class="min-h-0 flex flex-col gap-6 overflow-y-auto">
                <livewire:monitoring.vehicle-status />
                <livewire:monitoring.alerts-panel />
                <livewire:monitoring.event-timeline />
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let autoRefreshInterval = null;
        function startAutoRefresh() {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = setInterval(() => {
                Livewire.dispatch('refresh-monitoring');
            }, 30000);
        }
        document.addEventListener('livewire:navigated', startAutoRefresh);
        document.addEventListener('livewire:navigating', () => clearInterval(autoRefreshInterval));
        if (document.readyState === 'complete') startAutoRefresh();
    </script>
    @endpush
</x-layouts.app>
