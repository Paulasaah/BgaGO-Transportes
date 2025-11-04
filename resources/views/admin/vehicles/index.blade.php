{{-- resources/views/admin/vehicles/index.blade.php --}}

@php
    use App\Services\MockDataService;
    
    $stats = MockDataService::getVehicleStats();
    $vehicles = MockDataService::getVehicles();
    
    $tableColumns = [
        ['label' => 'Vehículo'],
        ['label' => 'Tipo'],
        ['label' => 'Estado'],
        ['label' => 'Conductor'],
        ['label' => 'Kilometraje'],
        ['label' => 'Mantenimiento'],
        ['label' => 'Sede'],
        ['label' => 'Acciones', 'class' => 'text-right'],
    ];
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        {{-- Header --}}
        <x-page-header
            title="Gestión de Vehículos"
            subtitle="Administra la flota de vehículos de BgaGo"
            button-text="Nuevo Vehículo"
        />

        {{-- Estadísticas --}}
        <x-dashboard.stats-grid>
            <x-stats.card
                title="Total Vehículos"
                :value="$stats['total']"
                icon="bike"
                color="blue"
            />

            <x-stats.card
                title="Disponibles"
                :value="$stats['disponibles']"
                icon="check-circle"
                color="green"
            />

            <x-stats.card
                title="En Servicio"
                :value="$stats['en_servicio']"
                icon="truck"
                color="yellow"
            />

            <x-stats.card
                title="Mantenimiento"
                :value="$stats['mantenimiento']"
                icon="wrench"
                color="orange"
            />
        </x-dashboard.stats-grid>

        {{-- Tabla --}}
        <x-table.container>
            <x-slot:filters>
                <x-filters.bar search-placeholder="Buscar por placa, marca o modelo...">
                    <flux:select placeholder="Tipo" class="min-w-[140px]">
                        <option value="">Todos</option>
                        <option value="SUV">SUV</option>
                        <option value="Sedan">Sedán</option>
                        <option value="Van">Van</option>
                    </flux:select>

                    <flux:select placeholder="Estado" class="min-w-[140px]">
                        <option value="">Todos</option>
                        <option value="available">Disponible</option>
                        <option value="busy">En Servicio</option>
                        <option value="maintenance">Mantenimiento</option>
                    </flux:select>

                    <flux:button icon="funnel" variant="ghost">
                        Filtros
                    </flux:button>
                </x-filters.bar>
            </x-slot:filters>

            <table class="w-full">
                <x-table.header :columns="$tableColumns" />
                
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @foreach($vehicles as $vehicle)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                                    <flux:icon.cube class="size-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $vehicle['placa'] }}
                                    </div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $vehicle['marca'] }} {{ $vehicle['modelo'] }} ({{ $vehicle['year'] }})
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $vehicle['tipo'] }}
                                </span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                    ({{ $vehicle['capacidad'] }} pax)
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <x-badges.status 
                                :status="$vehicle['status']"
                                :label="MockDataService::getStatusLabel($vehicle['status'])"
                            />
                        </td>
                        <td class="px-4 py-4">
                            @if($vehicle['conductor_asignado'])
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $vehicle['conductor_asignado'] }}
                                </div>
                            @else
                                <span class="text-sm text-zinc-400 dark:text-zinc-500 italic">
                                    Sin asignar
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                {{ number_format($vehicle['kilometraje']) }} km
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-xs">
                                <div class="text-zinc-500 dark:text-zinc-400">
                                    Último: {{ date('d/m/Y', strtotime($vehicle['ultimo_mantenimiento'])) }}
                                </div>
                                <div class="text-zinc-500 dark:text-zinc-400">
                                    Próximo: {{ date('d/m/Y', strtotime($vehicle['proximo_mantenimiento'])) }}
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $vehicle['sede'] }}
                            </span>
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
                    :current-count="count($vehicles)" 
                    :total="$stats['total']"
                    label="vehículos"
                />
            </x-slot:pagination>
        </x-table.container>

    </div>
</x-layouts.app>