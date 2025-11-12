@php
use App\Enums\VehicleStatus;
use App\Enums\VehicleType;
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">

        {{-- Header con acciones --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:button 
                    href="{{ route('admin.vehicles.index') }}" 
                    variant="ghost" 
                    icon="arrow-left"
                    size="sm"
                >
                    Volver
                </flux:button>
                <div>
                    <flux:heading size="xl">Detalle del Vehículo</flux:heading>
                    <flux:subheading>
                        Placa: <span class="font-mono">{{ $vehicle->placa }}</span>
                    </flux:subheading>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <flux:button 
                    href="{{ route('admin.vehicles.edit', $vehicle) }}" 
                    icon="pencil"
                    variant="primary"
                >
                    Editar
                </flux:button>

                <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" 
                      onsubmit="return confirm('¿Estás seguro de eliminar este vehículo?')">
                    @csrf
                    @method('DELETE')
                    <flux:button type="submit" variant="danger" icon="trash">
                        Eliminar
                    </flux:button>
                </form>
            </div>
        </div>

        {{-- Estado y tipo --}}
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-zinc-600 dark:text-zinc-400">Estado:</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-full font-medium 
                    @class([
                        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' => $vehicle->estado === VehicleStatus::Disponible,
                        'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' => $vehicle->estado === VehicleStatus::Ocupado,
                        'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' => $vehicle->estado === VehicleStatus::Mantenimiento,
                        'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' => $vehicle->estado === VehicleStatus::Inactivo,
                    ])
                ">
                    <flux:icon.circle class="size-3" />
                    {{ $vehicle->estado->label() }}
                </span>
            </div>

            <div class="h-6 w-px bg-zinc-300 dark:bg-zinc-700"></div>

            <div class="flex items-center gap-2">
                <span class="text-sm text-zinc-600 dark:text-zinc-400">Tipo:</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-sm font-medium
                    @if($vehicle->tipo === VehicleType::Carro)
                        bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                    @elseif($vehicle->tipo === VehicleType::Moto)
                        bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400
                    @else
                        bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300
                    @endif
                ">
                    <flux:icon.truck class="size-4" />
                    {{ $vehicle->tipo->label() }}
                </span>
            </div>
        </div>

        {{-- Información principal --}}
        <div class="grid gap-6 lg:grid-cols-3">
            
            {{-- Información general --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="size-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <flux:icon.truck class="size-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <flux:heading size="lg">Información General</flux:heading>
                </div>

                <div class="space-y-3">
                    <x-info-row label="Marca" :value="$vehicle->marca" />
                    <x-info-row label="Modelo" :value="$vehicle->modelo" />
                    <x-info-row label="Año" :value="$vehicle->year" />
                    <x-info-row label="Color" :value="$vehicle->color" />
                    <x-info-row label="Precio por hora" :value="'$' . number_format($vehicle->precio_hora, 0, ',', '.')" />
                    <x-info-row label="Precio por día" :value="'$' . number_format($vehicle->precio_dia, 0, ',', '.')" />

                    @if($vehicle->imagen_principal)
                        <div class="pt-4">
                            <img src="{{ asset('storage/' . $vehicle->imagen_principal) }}" alt="Imagen del vehículo" class="rounded-lg shadow-md">
                        </div>
                    @endif
                </div>
            </div>

            {{-- Conductor --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="size-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                        <flux:icon.user-circle class="size-5 text-orange-600 dark:text-orange-400" />
                    </div>
                    <flux:heading size="lg">Conductor Asignado</flux:heading>
                </div>

                @if($vehicle->driver)
                    <div class="space-y-3">
                        <x-info-row label="Nombre" :value="$vehicle->driver->name" />
                        <x-info-row label="Correo" :value="$vehicle->driver->email" />
                        @if($vehicle->driver->phone)
                            <x-info-row label="Teléfono" :value="$vehicle->driver->phone" />
                        @endif
                        @if($vehicle->driver->driverProfile?->license_number)
                            <x-info-row label="Licencia" :value="$vehicle->driver->driverProfile->license_number" />
                        @endif
                    </div>
                @else
                    <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                        <flux:icon.exclamation-triangle class="size-8 mx-auto mb-2 opacity-50" />
                        <p class="text-sm">Sin conductor asignado</p>
                    </div>
                @endif
            </div>

            {{-- Sede --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="size-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <flux:icon.map-pin class="size-5 text-green-600 dark:text-green-400" />
                    </div>
                    <flux:heading size="lg">Sede</flux:heading>
                </div>

                @if($vehicle->branch)
                    <x-info-row label="Nombre" :value="$vehicle->branch->nombre" />
                    @if($vehicle->branch->direccion)
                        <x-info-row label="Dirección" :value="$vehicle->branch->direccion" />
                    @endif
                @else
                    <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                        <flux:icon.exclamation-triangle class="size-8 mx-auto mb-2 opacity-50" />
                        <p class="text-sm">Sin sede asignada</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Estadísticas --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="size-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <flux:icon.bar-chart class="size-5 text-purple-600 dark:text-purple-400" />
                </div>
                <flux:heading size="lg">Estadísticas</flux:heading>
            </div>

            <div class="grid sm:grid-cols-4 gap-4 text-center">
                <div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">Total Reservas</div>
                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                        {{ $vehicle->reservations->count() }}
                    </div>
                </div>
                <div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">Completadas</div>
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ $vehicle->reservations->completadas()->count() }}
                    </div>
                </div>
                <div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">Promedio Calificación</div>
                    <div class="text-2xl font-bold text-yellow-500">
                        {{ $vehicle->getAverageRating() ?? 'N/A' }}
                    </div>
                </div>
                <div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">Total Ingresos</div>
                    <div class="text-2xl font-bold text-emerald-500">
                        ${{ number_format($vehicle->getTotalIngresos(), 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Últimas reservas --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="size-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <flux:icon.calendar class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
                <flux:heading size="lg">Últimas Reservas</flux:heading>
            </div>

            @if($vehicle->reservations->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-zinc-600 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-800">
                            <tr>
                                <th class="text-left py-2">Código</th>
                                <th class="text-left py-2">Cliente</th>
                                <th class="text-left py-2">Estado</th>
                                <th class="text-left py-2">Inicio</th>
                                <th class="text-left py-2">Fin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @foreach($vehicle->reservations as $reservation)
                                <tr>
                                    <td class="py-2 font-mono text-blue-600 dark:text-blue-400">{{ $reservation->codigo }}</td>
                                    <td class="py-2">{{ $reservation->user->name }}</td>
                                    <td class="py-2">{{ $reservation->estado->label() }}</td>
                                    <td class="py-2">{{ $reservation->fecha_inicio->format('d/m/Y') }}</td>
                                    <td class="py-2">{{ $reservation->fecha_fin->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                    <flux:icon.inbox class="size-8 mx-auto mb-2 opacity-50" />
                    <p class="text-sm">No hay reservas recientes.</p>
                </div>
            @endif
        </div>

        {{-- Descripción --}}
        @if($vehicle->descripcion)
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-2">Descripción</flux:heading>
                <p class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed">
                    {{ $vehicle->descripcion }}
                </p>
            </div>
        @endif
    </div>
</x-layouts.app>
