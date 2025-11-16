@php
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;

// Obtener mantenimientos con filtros y paginación
$maintenances = \App\Models\VehicleMaintenance::with(['vehicle', 'user'])
    ->when(request('search'), function($query, $search) {
        $query->whereHas('vehicle', fn($q) => $q->where('placa', 'like', "%{$search}%"))
              ->orWhere('descripcion', 'like', "%{$search}%")
              ->orWhere('taller', 'like', "%{$search}%");
    })
    ->when(request('tipo'), function($query, $tipo) {
        $query->where('tipo', $tipo);
    })
    ->when(request('estado'), function($query, $estado) {
        $query->where('estado', $estado);
    })
    ->orderByDesc('created_at')
    ->paginate(20);

// Estadísticas
$stats = [
    'total' => \App\Models\VehicleMaintenance::count(),
    'programados' => \App\Models\VehicleMaintenance::where('estado', MaintenanceStatus::Programado)->count(),
    'en_proceso' => \App\Models\VehicleMaintenance::where('estado', MaintenanceStatus::EnProceso)->count(),
    'completados' => \App\Models\VehicleMaintenance::where('estado', MaintenanceStatus::Completado)->count(),
];
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Gestión de Mantenimientos</flux:heading>
                <flux:subheading>Administra el mantenimiento preventivo y correctivo de vehículos</flux:subheading>
            </div>
            
            <flux:button :href="route('admin.maintenances.create')" icon="plus" variant="primary">
                Registrar Mantenimiento
            </flux:button>
        </div>

        {{-- Estadísticas --}}
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <x-stats.card 
                title="Total Mantenimientos" 
                :value="$stats['total']" 
                icon="wrench" 
                color="blue" 
            />
            <x-stats.card 
                title="Programados" 
                :value="$stats['programados']" 
                icon="calendar" 
                color="yellow" 
            />
            <x-stats.card 
                title="En Proceso" 
                :value="$stats['en_proceso']" 
                icon="cog" 
                color="blue" 
            />
            <x-stats.card 
                title="Completados" 
                :value="$stats['completados']" 
                icon="check-circle" 
                color="green" 
            />
        </div>

        {{-- Filtros --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-4">
            <form method="GET" class="flex flex-wrap gap-3">
                <flux:input 
                    name="search" 
                    placeholder="Buscar por placa, descripción o taller..."
                    value="{{ request('search') }}"
                    class="flex-1 min-w-[200px]"
                />
                
                <flux:select name="tipo" placeholder="Tipo" class="min-w-[180px]">
                    <option value="">Todos los tipos</option>
                    @foreach(MaintenanceType::cases() as $tipo)
                        <option value="{{ $tipo->value }}" {{ request('tipo') === $tipo->value ? 'selected' : '' }}>
                            {{ $tipo->label() }}
                        </option>
                    @endforeach
                </flux:select>

                <flux:select name="estado" placeholder="Estado" class="min-w-[150px]">
                    <option value="">Todos los estados</option>
                    @foreach(MaintenanceStatus::cases() as $estado)
                        <option value="{{ $estado->value }}" {{ request('estado') === $estado->value ? 'selected' : '' }}>
                            {{ $estado->label() }}
                        </option>
                    @endforeach
                </flux:select>

                <flux:button type="submit" icon="magnifying-glass">
                    Buscar
                </flux:button>
                
                @if(request()->hasAny(['search', 'tipo', 'estado']))
                    <flux:button href="{{ url()->current() }}" variant="ghost">
                        Limpiar
                    </flux:button>
                @endif
            </form>
        </div>

        {{-- Tabla --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            @if($maintenances->isEmpty())
                <div class="text-center py-12 text-zinc-500 dark:text-zinc-400">
                    <flux:icon.wrench class="size-12 mx-auto mb-3 opacity-50" />
                    <p class="text-lg font-medium">No hay mantenimientos registrados</p>
                    <p class="text-sm mt-2">Comienza registrando un nuevo mantenimiento</p>
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
                                    Tipo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Descripción
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Costo
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                            @foreach($maintenances as $maintenance)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                            <flux:icon.truck class="size-5 text-blue-600 dark:text-blue-400" />
                                        </div>
                                        <div>
                                            <div class="font-mono font-semibold text-zinc-900 dark:text-zinc-100">
                                                {{ $maintenance->vehicle->placa }}
                                            </div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ $maintenance->vehicle->marca }} {{ $maintenance->vehicle->modelo }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-{{ $maintenance->tipo->color() }}-100 dark:bg-{{ $maintenance->tipo->color() }}-900/30 text-{{ $maintenance->tipo->color() }}-700 dark:text-{{ $maintenance->tipo->color() }}-400">
                                        {{ $maintenance->tipo->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100 max-w-xs truncate">
                                        {{ $maintenance->descripcion }}
                                    </div>
                                    @if($maintenance->taller)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $maintenance->taller }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $maintenance->fecha_programada?->format('d/m/Y') ?? 'Sin fecha' }}
                                    </div>
                                    @if($maintenance->fecha_realizada)
                                        <div class="text-xs text-green-600 dark:text-green-400">
                                            Realizado: {{ $maintenance->fecha_realizada->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs rounded-full font-medium bg-{{ $maintenance->estado->color() }}-100 text-{{ $maintenance->estado->color() }}-700 dark:bg-{{ $maintenance->estado->color() }}-900/30 dark:text-{{ $maintenance->estado->color() }}-400">
                                        {{ $maintenance->estado->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                        ${{ number_format($maintenance->costo, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button 
                                            :href="route('admin.maintenances.show', $maintenance)"
                                            size="sm" 
                                            variant="ghost" 
                                            icon="eye"
                                            title="Ver detalles"
                                            class="text-zinc-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400"
                                        />
                                        
                                        @if($maintenance->canEdit())
                                            <flux:button 
                                                :href="route('admin.maintenances.edit', $maintenance)"
                                                size="sm" 
                                                variant="ghost" 
                                                icon="pencil"
                                                title="Editar"
                                                class="text-zinc-600 dark:text-zinc-400 hover:text-green-600 dark:hover:text-green-400"
                                            />
                                        @endif
                                        
                                        @if($maintenance->estado !== MaintenanceStatus::Completado)
                                            <form method="POST" action="{{ route('admin.maintenances.destroy', $maintenance) }}" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este mantenimiento?')">
                                                @csrf
                                                @method('DELETE')
                                                <flux:button 
                                                    type="submit"
                                                    size="sm" 
                                                    variant="ghost" 
                                                    icon="trash"
                                                    title="Eliminar"
                                                    class="text-zinc-600 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400"
                                                />
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $maintenances->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>
