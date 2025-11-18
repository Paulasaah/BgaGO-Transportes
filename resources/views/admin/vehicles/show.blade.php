@php
$reservasCount = $vehicle->reservations()->count();
$reservasActivas = $vehicle->reservations()->whereIn('estado', ['pendiente', 'confirmada', 'activa'])->count();
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="size-16 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                    <flux:icon.truck class="size-8 text-white" />
                </div>
                
                <div>
                    <flux:heading size="xl">{{ $vehicle->placa }}</flux:heading>
                    <flux:subheading>{{ $vehicle->marca }} {{ $vehicle->modelo }} ({{ $vehicle->year }})</flux:subheading>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <flux:button :href="route('admin.vehicles.edit', $vehicle)" variant="primary" icon="pencil">
                    Editar
                </flux:button>
                
                <flux:button :href="route('admin.vehicles.index')" variant="ghost" icon="arrow-left">
                    Volver
                </flux:button>
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <x-stats.card title="Total Reservas" :value="$reservasCount" icon="clipboard-document-list" color="blue" />
            <x-stats.card title="Reservas Activas" :value="$reservasActivas" icon="clock" color="green" />
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <flux:heading size="lg" class="mb-4">Información del Vehículo</flux:heading>
            
            <div class="space-y-4">
                <div class="flex justify-between py-3 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Placa</span>
                    <span class="text-zinc-900 dark:text-zinc-100 font-mono">{{ $vehicle->placa }}</span>
                </div>
                
                <div class="flex justify-between py-3 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Marca / Modelo</span>
                    <span class="text-zinc-900 dark:text-zinc-100">{{ $vehicle->marca }} {{ $vehicle->modelo }}</span>
                </div>
                
                <div class="flex justify-between py-3 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Año</span>
                    <span class="text-zinc-900 dark:text-zinc-100">{{ $vehicle->year }}</span>
                </div>
                
                <div class="flex justify-between py-3 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Tipo</span>
                    <span class="text-zinc-900 dark:text-zinc-100">{{ $vehicle->tipo->label() }}</span>
                </div>
                
                <div class="flex justify-between py-3 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Estado</span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium
                        @if($vehicle->estado->value === 'disponible')
                            bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                        @elseif($vehicle->estado->value === 'ocupado')
                            bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400
                        @else
                            bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400
                        @endif
                    ">
                        {{ $vehicle->estado->label() }}
                    </span>
                </div>
                
                @if($vehicle->driver)
                <div class="flex justify-between py-3 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Conductor Asignado</span>
                    <span class="text-zinc-900 dark:text-zinc-100">{{ $vehicle->driver->name }}</span>
                </div>
                @endif
                
                @if($vehicle->branch)
                <div class="flex justify-between py-3 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Sede</span>
                    <span class="text-zinc-900 dark:text-zinc-100">{{ $vehicle->branch->nombre }}</span>
                </div>
                @endif
                
                <div class="flex justify-between py-3">
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Registrado</span>
                    <span class="text-zinc-900 dark:text-zinc-100">{{ $vehicle->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>