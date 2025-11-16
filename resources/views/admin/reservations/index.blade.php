@php
use App\Facades\Data;
use App\Enums\ReservationStatus;

// Obtener estadísticas
$stats = Data::getReservationStats();

// Obtener reservas con filtros y paginación
$reservations = \App\Models\Reservation::with(['user', 'vehicle', 'driver'])
    ->when(request('search'), function($query, $search) {
        $query->where('codigo', 'like', "%{$search}%")
              ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
    })
    ->when(request('tipo'), function($query, $tipo) {
        $query->where('tipo', $tipo);
    })
    ->when(request('estado'), function($query, $estado) {
        $query->where('estado', $estado);
    })
    ->when(request('fecha'), function($query, $fecha) {
        $query->whereDate('fecha_inicio', $fecha);
    })
    ->orderByDesc('created_at')
    ->paginate(20);
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Gestión de Reservas</flux:heading>
                <flux:subheading>Visualiza y controla todas las reservas activas, completadas o canceladas</flux:subheading>
            </div>
            
            <flux:button :href="route('admin.reservations.create')" icon="plus" variant="primary">
                Crear Reserva
            </flux:button>
        </div>

        {{-- Estadísticas --}}
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <x-stats.card 
                title="Total Reservas" 
                :value="$stats['total']" 
                icon="clipboard-document-list" 
                color="blue" 
            />
            <x-stats.card 
                title="Activas" 
                :value="$stats['activas']" 
                icon="clock" 
                color="green" 
            />
            <x-stats.card 
                title="Completadas" 
                :value="$stats['completadas']" 
                icon="check-badge" 
                color="purple" 
            />
            <x-stats.card 
                title="Canceladas" 
                :value="$stats['canceladas']" 
                icon="x-circle" 
                color="red" 
            />
        </div>

        {{-- Filtros --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-4">
            <form method="GET" class="flex flex-wrap gap-3">
                <flux:input 
                    name="search" 
                    placeholder="Buscar por código o usuario..."
                    value="{{ request('search') }}"
                    class="flex-1 min-w-[200px]"
                />
                
                <flux:select name="tipo" placeholder="Tipo" class="min-w-[140px]">
                    <option value="">Todos</option>
                    <option value="reserva" {{ request('tipo') === 'reserva' ? 'selected' : '' }}>Reserva</option>
                    <option value="domicilio" {{ request('tipo') === 'domicilio' ? 'selected' : '' }}>Domicilio</option>
                </flux:select>

                <flux:select name="estado" placeholder="Estado" class="min-w-[150px]">
                    <option value="">Todos</option>
                    <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmada" {{ request('estado') === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                    <option value="activa" {{ request('estado') === 'activa' ? 'selected' : '' }}>Activa</option>
                    <option value="completada" {{ request('estado') === 'completada' ? 'selected' : '' }}>Completada</option>
                    <option value="cancelada" {{ request('estado') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </flux:select>

                <flux:input 
                    type="date" 
                    name="fecha" 
                    value="{{ request('fecha') }}"
                    class="min-w-[140px]"
                />

                <flux:button type="submit" icon="magnifying-glass">
                    Buscar
                </flux:button>
                
                @if(request()->hasAny(['search', 'tipo', 'estado', 'fecha']))
                    <flux:button href="{{ url()->current() }}" variant="ghost">
                        Limpiar
                    </flux:button>
                @endif
            </form>
        </div>

        {{-- Tabla --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            @if($reservations->isEmpty())
                <div class="text-center py-12 text-zinc-500 dark:text-zinc-400">
                    <flux:icon.clipboard-document-list class="size-12 mx-auto mb-3 opacity-50" />
                    <p class="text-lg font-medium">No hay reservas registradas</p>
                    <p class="text-sm mt-2">Comienza creando una nueva reserva</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Código
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Usuario
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Vehículo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Conductor
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Tipo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Monto
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                            @foreach($reservations as $reserva)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $reserva->codigo }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $reserva->user->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $reserva->user->email ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($reserva->vehicle)
                                        <span class="font-mono text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ $reserva->vehicle->placa }}
                                        </span>
                                    @else
                                        <span class="text-zinc-400 dark:text-zinc-500 text-xs italic">
                                            Sin asignar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($reserva->driver)
                                        <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ $reserva->driver->name }}
                                        </div>
                                    @else
                                        <span class="text-zinc-400 dark:text-zinc-500 text-xs italic">
                                            Sin asignar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium
                                        @if($reserva->tipo->value === 'reserva')
                                            bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                                        @else
                                            bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400
                                        @endif
                                    ">
                                        {{ ucfirst($reserva->tipo->value) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $reserva->fecha_inicio->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $reserva->fecha_inicio->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs rounded-full font-medium
                                        @if($reserva->estado === ReservationStatus::Activa)
                                            bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                        @elseif($reserva->estado === ReservationStatus::Completada)
                                            bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($reserva->estado === ReservationStatus::Pendiente || $reserva->estado === ReservationStatus::Confirmada)
                                            bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                        @else
                                            bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                        @endif
                                    ">
                                        {{ ucfirst($reserva->estado->label()) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                        ${{ number_format($reserva->monto_final ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button 
                                            :href="route('admin.reservations.show', $reserva)"
                                            size="sm" 
                                            variant="ghost" 
                                            icon="eye"
                                            title="Ver detalles"
                                            class="text-zinc-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400"
                                        />
                                        @if($reserva->estado !== ReservationStatus::Completada && $reserva->estado !== ReservationStatus::Cancelada)
                                            <form method="POST" action="{{ route('admin.reservations.cancel', $reserva) }}" class="inline" onsubmit="return confirm('¿Estás seguro de cancelar la reserva {{ $reserva->codigo }}?')">
                                                @csrf
                                                @method('PATCH')
                                                <flux:button 
                                                    type="submit"
                                                    size="sm" 
                                                    variant="ghost" 
                                                    icon="x-mark"
                                                    title="Cancelar reserva"
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
                    {{ $reservations->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>