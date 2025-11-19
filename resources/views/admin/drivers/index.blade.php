@php
use App\Facades\Data;

// Obtener estadísticas
$stats = Data::getDriverStats();

// Obtener conductores con filtros y paginación
$drivers = \App\Models\User::role('conductor')
    ->with(['driverProfile'])
    ->when(request('search'), function($query, $search) {
        $query->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhereHas('driverProfile', function($q) use ($search) {
                  $q->where('license_number', 'like', "%{$search}%");
              });
    })
    ->when(request('estado'), function($query, $estado) {
        if ($estado === 'activo') {
            $query->whereHas('driverProfile', fn($q) => $q->where('is_active', true));
        } elseif ($estado === 'inactivo') {
            $query->whereHas('driverProfile', fn($q) => $q->where('is_active', false))
                  ->orWhereDoesntHave('driverProfile');
        }
    })
    ->orderBy('name')
    ->paginate(15);

// Obtener vehículo asignado para cada conductor (CORREGIDO)
foreach ($drivers as $driver) {
    $driver->assignedVehicle = \App\Models\Vehicle::where('conductor_id', $driver->id)->first();
}
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Gestión de Conductores</flux:heading>
                <flux:subheading>Control de conductores activos e inactivos</flux:subheading>
            </div>
        </div>

        {{-- Estadísticas --}}
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <x-stats.card 
                title="Total Conductores" 
                :value="$stats['total']" 
                icon="user-circle" 
                color="blue" 
            />
            <x-stats.card 
                title="Activos" 
                :value="$stats['activos']" 
                icon="check-badge" 
                color="green" 
            />
            <x-stats.card 
                title="Inactivos" 
                :value="$stats['inactivos']" 
                icon="x-circle" 
                color="red" 
            />
        </div>

        {{-- Filtros --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-4">
            <form method="GET" class="flex flex-wrap gap-3">
                <flux:input 
                    name="search" 
                    placeholder="Buscar por nombre, email o licencia..."
                    value="{{ request('search') }}"
                    class="flex-1 min-w-[200px]"
                />
                
                <flux:select name="estado" placeholder="Estado" class="min-w-[140px]">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activos</option>
                    <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                </flux:select>

                <flux:button type="submit" icon="magnifying-glass">
                    Buscar
                </flux:button>
                
                @if(request()->hasAny(['search', 'estado']))
                    <flux:button href="{{ url()->current() }}" variant="ghost">
                        Limpiar
                    </flux:button>
                @endif
            </form>
        </div>

        {{-- Tabla Mejorada --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            @if($drivers->isEmpty())
                <div class="text-center py-12 text-zinc-500 dark:text-zinc-400">
                    <flux:icon.user-circle class="size-12 mx-auto mb-3 opacity-50" />
                    <p class="text-lg font-medium">No hay conductores registrados</p>
                    <p class="text-sm mt-2">Asigna el rol 'conductor' a un usuario para verlo aquí</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Conductor
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Contacto
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Licencia
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Vehículo Asignado
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Calificación
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
                            @foreach($drivers as $driver)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                {{-- Conductor --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="size-10 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center flex-shrink-0">
                                            <span class="text-white font-semibold text-sm">
                                                {{ strtoupper(substr($driver->name, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $driver->name }}
                                            </div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                ID: {{ $driver->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Contacto --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $driver->email }}
                                    </div>
                                    @if($driver->phone)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                            {{ $driver->phone }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Licencia --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($driver->driverProfile?->license_number)
                                        <div class="flex flex-col">
                                            <span class="font-mono text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $driver->driverProfile->license_number }}
                                            </span>
                                            @if($driver->driverProfile->license_expiry)
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                                    Vence: {{ \Carbon\Carbon::parse($driver->driverProfile->license_expiry)->format('d/m/Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-zinc-400 dark:text-zinc-500 text-xs italic">
                                            Sin licencia
                                        </span>
                                    @endif
                                </td>

                                {{-- Vehículo --}}
                                <td class="px-6 py-4">
                                    @if($driver->assignedVehicle)
                                        <div class="flex items-center gap-2">
                                            <div class="size-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <flux:icon.truck class="size-4 text-blue-600 dark:text-blue-400" />
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold font-mono text-zinc-900 dark:text-zinc-100">
                                                    {{ $driver->assignedVehicle->placa }}
                                                </div>
                                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    {{ $driver->assignedVehicle->marca }} {{ $driver->assignedVehicle->modelo }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-zinc-400 dark:text-zinc-500 italic">
                                            Sin asignar
                                        </span>
                                    @endif
                                </td>

                                {{-- Calificación --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($driver->driverProfile?->rating)
                                        <div class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-yellow-50 dark:bg-yellow-900/20">
                                            <flux:icon.star class="size-4 text-yellow-500" />
                                            <span class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">
                                                {{ number_format($driver->driverProfile->rating, 1) }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-zinc-400 dark:text-zinc-500 text-xs">
                                            Sin calificación
                                        </span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($driver->driverProfile?->is_active ?? false)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            <span class="size-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                            <span class="size-1.5 rounded-full bg-red-500"></span>
                                            Inactivo
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
                                            href="{{ route('admin.drivers.show', $driver) }}"
                                        />
                                        <flux:button 
                                            size="sm" 
                                            variant="ghost" 
                                            icon="pencil"
                                            title="Editar"
                                            href="{{ route('admin.drivers.edit', $driver) }}"
                                        />
                                        <form action="{{ route('admin.drivers.toggle-status', $driver) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <flux:button 
                                                size="sm" 
                                                variant="ghost" 
                                                icon="power"
                                            >{{ ($driver->driverProfile?->is_active ?? false) ? 'Desactivar' : 'Activar' }}</flux:button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $drivers->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>