{{-- resources/views/admin/drivers/index.blade.php --}}

@php
    use App\Services\MockDataService;
    
    $stats = MockDataService::getDriverStats();
    $drivers = MockDataService::getDrivers();
    
    $tableColumns = [
        ['label' => 'Conductor'],
        ['label' => 'Licencia'],
        ['label' => 'Estado'],
        ['label' => 'Vehículo'],
        ['label' => 'Servicios'],
        ['label' => 'Calificación'],
        ['label' => 'Última Actividad'],
        ['label' => 'Sede'],
        ['label' => 'Acciones', 'class' => 'text-right'],
    ];
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        {{-- Header --}}
        <x-page-header
            title="Gestión de Conductores"
            subtitle="Administra los conductores de BgaGo"
            button-text="Nuevo Conductor"
        />

        {{-- Estadísticas --}}
        <x-dashboard.stats-grid>
            <x-stats.card
                title="Total Conductores"
                :value="$stats['total']"
                icon="user-circle"
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
                title="Inactivos"
                :value="$stats['inactivos']"
                icon="x-circle"
                color="red"
            />
        </x-dashboard.stats-grid>

        {{-- Tabla --}}
        <x-table.container>
            <x-slot:filters>
                <x-filters.bar search-placeholder="Buscar por nombre o licencia...">
                    <flux:select placeholder="Estado" class="min-w-[140px]">
                        <option value="">Todos</option>
                        <option value="available">Disponible</option>
                        <option value="busy">Ocupado</option>
                        <option value="inactive">Inactivo</option>
                    </flux:select>

                    <flux:select placeholder="Sede" class="min-w-[140px]">
                        <option value="">Todas</option>
                        <option value="Norte">Norte</option>
                        <option value="Sur">Sur</option>
                        <option value="Centro">Centro</option>
                        <option value="Oriente">Oriente</option>
                    </flux:select>

                    <flux:button icon="funnel" variant="ghost">
                        Filtros
                    </flux:button>
                </x-filters.bar>
            </x-slot:filters>

            <table class="w-full">
                <x-table.header :columns="$tableColumns" />
                
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @foreach($drivers as $driver)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                                    <flux:icon.user-circle class="size-5 text-green-600 dark:text-green-400" />
                                </div>
                                <div>
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $driver['name'] }}
                                    </div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $driver['email'] }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="font-mono text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $driver['license'] }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <x-badges.status 
                                :status="$driver['status']"
                                :label="MockDataService::getStatusLabel($driver['status'])"
                            />
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $driver['vehiculo_asignado'] }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm">
                                <span class="font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ $driver['servicios_completados'] }}
                                </span>
                                <span class="text-zinc-500 dark:text-zinc-400"> completados</span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                <flux:icon.star class="size-4 text-yellow-500" />
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $driver['calificacion'] }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ date('d/m/Y H:i', strtotime($driver['ultima_actividad'])) }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $driver['sede'] }}
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
                    :current-count="count($drivers)" 
                    :total="$stats['total']"
                    label="conductores"
                />
            </x-slot:pagination>
        </x-table.container>

    </div>
</x-layouts.app>