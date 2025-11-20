<x-layouts.app>
    <div class="flex flex-col h-screen max-h-screen overflow-hidden w-full gap-6 p-6 lg:p-8">
        <!-- Header -->
        <div class="flex-shrink-0">
            <flux:heading size="xl">Mapa de Vehículos</flux:heading>
            <flux:subheading>Monitoreo de ubicaciones en tiempo real</flux:subheading>
        </div>

        <!-- Componente del Mapa -->
        <div class="flex-1 min-h-0">
            <livewire:map.map-view :reservationId="request('reservation')" />
        </div>
    </div>
</x-layouts.app>
