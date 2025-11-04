{{-- resources/views/admin/reservations/index.blade.php --}}

@php
    use App\Services\MockDataService;
    
    $stats = MockDataService::getReservationStats();
    $reservations = MockDataService::getReservations();
    
    $tableColumns = [
        ['label' => 'Código'],
        ['label' => 'Cliente'],
        ['label' => 'Ruta'],
        ['label' => 'Conductor/Vehículo'],
        ['label' => 'Fecha/Hora'],
        ['label' => 'Tipo'],
        ['label' => 'Estado'],
        ['label' => 'Monto'],
        ['label' => 'Acciones', 'class' => 'text-right'],
    ];
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        {{-- Header --}}
        <x-page-header
            title="Gestión de Reservas"
            subtitle="Administra todas las reservas y domicilios"
            button-text="Nueva Reserva"
        />

        {{-- Estadísticas --}}
        <x-dashboard.stats-grid>
            <x-stats.card
                title="Pendientes"
                :value="$stats['pendientes']"
                icon="clock"
                color="blue"
            />

            <x-stats.card
                title="Activas"
                :value="$stats['activas']"
                icon="clipboard-check"
                color="green"
            />

            <x-stats.card
                title="Completadas Hoy"
                :value="$stats['completadas_hoy']"
                icon="check-circle"
                color="purple"
            />

            <x-stats.card
                title="Canceladas (Mes)"
                :value="$stats['canceladas_mes']"
                icon="x-circle"
                color="red"
            />
        </x-dashboard.stats-grid>

        {{-- Tabla --}}
        <x-table.container>
            <x-slot:filters>
                <x-filters.bar search-placeholder="Buscar por código o usuario...">
                    <flux:select placeholder="Tipo" class="min-w-[140px]">
                        <option value="">Todos</option>
                        <option value="reserva">Reserva</option>
                        <option value="domicilio">Domicilio</option>
                    </flux:select>

                    <flux:select placeholder="Estado" class="min-w-[140px]">
                        <option value="">Todos</option>
                        <option value="pending">Pendiente</option>
                        <option value="active">Activa</option>
                        <option value="completed">Completada</option>
                        <option value="cancelled">Cancelada</option>
                    </flux:select>

                    <flux:button icon="calendar" variant="ghost">
                        Fecha
                    </flux:button>
                </x-filters.bar>
            </x-slot:filters>

            <table class="w-full">
                <x-table.header :columns="$tableColumns" />
                
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @foreach($reservations as $reservation)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="px-4 py-4">
                            <div class="font-mono text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $reservation['codigo'] }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $reservation['usuario'] }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="max-w-xs">
                                <div class="flex items-start gap-2 text-xs">
                                    <flux:icon.map-pin class="size-3 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" />
                                    <span class="text-zinc-600 dark:text-zinc-400 line-clamp-1">
                                        {{ $reservation['origen'] }}
                                    </span>
                                </div>
                                <div class="flex items-start gap-2 text-xs mt-1">
                                    <flux:icon.flag class="size-3 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" />
                                    <span class="text-zinc-600 dark:text-zinc-400 line-clamp-1">
                                        {{ $reservation['destino'] }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            @if($reservation['conductor'])
                                <div class="text-xs">
                                    <div class="text-zinc-900 dark:text-zinc-100 font-medium">
                                        {{ $reservation['conductor'] }}
                                    </div>
                                    <div class="text-zinc-500 dark:text-zinc-400">
                                        {{ $reservation['vehiculo'] }}
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-zinc-400 dark:text-zinc-500 italic">
                                    Sin asignar
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-xs">
                                <div class="text-zinc-900 dark:text-zinc-100">
                                    {{ date('d/m/Y H:i', strtotime($reservation['fecha_inicio'])) }}
                                </div>
                                <div class="text-zinc-500 dark:text-zinc-400">
                                    {{ date('H:i', strtotime($reservation['fecha_fin'])) }}
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            @if($reservation['tipo'] === 'reserva')
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-medium">
                                    <flux:icon.clipboard-document-list class="size-3" />
                                    Reserva
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 text-xs font-medium">
                                    <flux:icon.truck class="size-3" />
                                    Domicilio
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <x-badges.status 
                                :status="$reservation['status']"
                                :label="MockDataService::getStatusLabel($reservation['status'])"
                            />
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                ${{ number_format($reservation['monto']) }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <x-table.actions />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <x-slot:pagination>
                <x-table.pagination 
                    :current-count="count($reservations)" 
                    :total="$stats['activas'] + $stats['pendientes']"
                    label="reservas"
                />
            </x-slot:pagination>
        </x-table.container>

    </div>
</x-layouts.app>