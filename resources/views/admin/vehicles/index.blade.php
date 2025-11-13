@php
use App\Facades\Data;
use App\Enums\VehicleStatus;

// Obtener estadísticas
$stats = Data::getVehicleStats();

// Obtener vehículos con filtros y paginación
$vehicles = \App\Models\Vehicle::with(['driver', 'branch'])
    ->when(request('search'), function($query, $search) {
        $query->where('placa', 'like', "%{$search}%")
              ->orWhere('marca', 'like', "%{$search}%")
              ->orWhere('modelo', 'like', "%{$search}%");
    })
    ->when(request('estado'), function($query, $estado) {
        $query->where('estado', $estado);
    })
    ->when(request('tipo'), function($query, $tipo) {
        $query->where('tipo', $tipo);
    })
    ->orderBy('placa')
    ->paginate(15);
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Gestión de Vehículos</flux:heading>
                <flux:subheading>Flota de transporte BgaGo</flux:subheading>
            </div>
        </div>

        {{-- Estadísticas --}}
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

        {{-- Filtros --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-4">
            <form method="GET" class="flex flex-wrap gap-3">
                <flux:input
                    name="search"
                    placeholder="Buscar por placa, marca o modelo..."
                    value="{{ request('search') }}"
                    class="flex-1 min-w-[200px]"
                />

                <flux:select name="estado" placeholder="Estado" class="min-w-[140px]">
                    <option value="">Todos</option>
                    <option value="disponible" {{ request('estado') === 'disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="ocupado" {{ request('estado') === 'ocupado' ? 'selected' : '' }}>Ocupado</option>
                    <option value="mantenimiento" {{ request('estado') === 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                </flux:select>

                <flux:select name="tipo" placeholder="Tipo" class="min-w-[140px]">
                    <option value="">Todos</option>
                    <option value="sedan" {{ request('tipo') === 'sedan' ? 'selected' : '' }}>Sedán</option>
                    <option value="suv" {{ request('tipo') === 'suv' ? 'selected' : '' }}>SUV</option>
                    <option value="van" {{ request('tipo') === 'van' ? 'selected' : '' }}>Van</option>
                    <option value="pickup" {{ request('tipo') === 'pickup' ? 'selected' : '' }}>Pickup</option>
                </flux:select>

                <flux:button type="submit" icon="magnifying-glass">
                    Buscar
                </flux:button>

                @if(request()->hasAny(['search', 'estado', 'tipo']))
                    <flux:button href="{{ url()->current() }}" variant="ghost">
                        Limpiar
                    </flux:button>
                @endif
            </form>
        </div>

        {{-- Tabla Mejorada --}}
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Vehículo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Detalles
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Conductor Asignado
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                            @foreach($vehicles as $vehicle)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                {{-- Vehículo --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="size-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center flex-shrink-0">
                                            <flux:icon.truck class="size-5 text-white" />
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold font-mono text-zinc-900 dark:text-zinc-100">
                                                {{ $vehicle->placa }}
                                            </div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                ID: {{ $vehicle->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Detalles --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $vehicle->marca }} {{ $vehicle->modelo }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs">
                                            {{ $vehicle->tipo->label() }}
                                        </span>
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $vehicle->year }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Conductor --}}
                                <td class="px-6 py-4">
                                    @if($vehicle->driver)
                                        <div class="flex items-center gap-2">
                                            <div class="size-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                                                <span class="text-green-600 dark:text-green-400 font-semibold text-xs">
                                                    {{ strtoupper(substr($vehicle->driver->name, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                                    {{ $vehicle->driver->name }}
                                                </div>
                                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    Conductor ID: {{ $vehicle->driver->id }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-zinc-400 dark:text-zinc-500 italic">
                                            Sin asignar
                                        </span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($vehicle->estado === VehicleStatus::Disponible)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            <span class="size-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                            Disponible
                                        </span>
                                    @elseif($vehicle->estado === VehicleStatus::Ocupado)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                            <span class="size-1.5 rounded-full bg-yellow-500 animate-pulse"></span>
                                            Ocupado
                                        </span>
                                    @elseif($vehicle->estado === VehicleStatus::Mantenimiento)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                            <span class="size-1.5 rounded-full bg-red-500"></span>
                                            Mantenimiento
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full font-medium bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                                            {{ $vehicle->estado->label() }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button
                                            size="sm"
                                            variant="ghost"
                                            icon="eye"
                                            title="Ver detalles"
                                            href="{{ route('admin.vehicles.show', $vehicle) }}"
                                            wire:navigate
                                        />
                                        <flux:button
                                            size="sm"
                                            variant="ghost"
                                            icon="pencil"
                                            title="Editar"
                                            href="{{ route('admin.vehicles.edit', $vehicle) }}"
                                            wire:navigate
                                        />
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $vehicles->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>
