@php
use App\Enums\VehicleStatus;
@endphp

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Gestión de Vehículos</flux:heading>
            <flux:subheading>Flota de transporte BgaGo</flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            <flux:button href="{{ route('admin.vehicles.create') }}" icon="plus" variant="primary">
                Añadir vehículo
            </flux:button>
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <x-stats.card 
            title="Total Vehículos" 
            :value="$stats['total']" 
            icon="truck" 
            color="blue" 
        />
        <x-stats.card 
            title="Disponibles" 
            :value="$stats['disponibles']" 
            icon="check-circle" 
            color="green" 
        />
        <x-stats.card 
            title="Ocupados" 
            :value="$stats['ocupados']" 
            icon="clock" 
            color="orange" 
        />
        <x-stats.card 
            title="Mantenimiento" 
            :value="$stats['mantenimiento']" 
            icon="wrench" 
            color="red" 
        />
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-4">
        <div class="flex flex-wrap gap-3">
            <flux:input 
                placeholder="Buscar por placa, marca o modelo..."
                class="flex-1 min-w-[200px]"
                wire:model.debounce.300ms="search"
            />

            <flux:select placeholder="Estado" class="min-w-[140px]" wire:model="estado">
                <option value="">Todos</option>
                <option value="disponible">Disponible</option>
                <option value="ocupado">Ocupado</option>
                <option value="mantenimiento">Mantenimiento</option>
            </flux:select>

            <flux:select placeholder="Tipo" class="min-w-[140px]" wire:model="tipo">
                <option value="">Todos</option>
                <option value="sedan">Sedán</option>
                <option value="suv">SUV</option>
                <option value="van">Van</option>
                <option value="pickup">Pickup</option>
            </flux:select>

            <flux:button variant="ghost" type="button" wire:click="$set('search',''); $set('estado',''); $set('tipo','')">
                Limpiar
            </flux:button>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden">
        @if($vehicles->isEmpty())
            <div class="text-center py-12 text-zinc-500 dark:text-zinc-400">
                <flux:icon.truck class="size-12 mx-auto mb-3 opacity-50" />
                <p class="text-lg font-medium">No hay vehículos registrados</p>
                <p class="text-sm mt-2">Comienza agregando un nuevo vehículo</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Vehículo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Detalles</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Conductor Asignado</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach($vehicles as $vehicle)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="size-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center flex-shrink-0">
                                        <flux:icon.truck class="size-5 text-white" />
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold font-mono text-zinc-900 dark:text-zinc-100">{{ $vehicle->placa }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">ID: {{ $vehicle->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $vehicle->marca }} {{ $vehicle->modelo }}</div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs">{{ $vehicle->tipo->label() }}</span>
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $vehicle->year }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($vehicle->driver)
                                    <div class="flex items-center gap-2">
                                        <div class="size-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                                            <span class="text-green-600 dark:text-green-400 font-semibold text-xs">{{ strtoupper(substr($vehicle->driver->name, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm text-zinc-900 dark:text-zinc-100">{{ $vehicle->driver->name }}</div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Conductor ID: {{ $vehicle->driver->id }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-zinc-400 dark:text-zinc-500 italic">Sin asignar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($vehicle->estado === VehicleStatus::Disponible)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400"><span class="size-1.5 rounded-full bg-green-500 animate-pulse"></span>Disponible</span>
                                @elseif($vehicle->estado === VehicleStatus::Ocupado)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400"><span class="size-1.5 rounded-full bg-yellow-500 animate-pulse"></span>Ocupado</span>
                                @elseif($vehicle->estado === VehicleStatus::Mantenimiento)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400"><span class="size-1.5 rounded-full bg-red-500"></span>Mantenimiento</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full font-medium bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">{{ $vehicle->estado->label() }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" icon="eye" title="Ver detalles" href="{{ route('admin.vehicles.show', $vehicle) }}" />
                                    <flux:button size="sm" variant="ghost" icon="pencil" title="Editar" href="{{ route('admin.vehicles.edit', $vehicle) }}" />
                                    <flux:button size="sm" variant="ghost" icon="trash" title="Eliminar" class="text-zinc-600 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400" wire:click="confirmDelete({{ $vehicle->id }})" />
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200 dark:border-zinc-800">
                {{ $vehicles->links() }}
            </div>
        @endif
    </div>
</div>