{{-- resources/views/admin/users/index.blade.php --}}

@php
    use App\Services\MockDataService;
    
    $stats = MockDataService::getUserStats();
    $users = MockDataService::getUsers();
    
    $tableColumns = [
        ['label' => 'Usuario'],
        ['label' => 'Contacto'],
        ['label' => 'Estado'],
        ['label' => 'Reservas'],
        ['label' => 'Última Reserva'],
        ['label' => 'Sede'],
        ['label' => 'Acciones', 'class' => 'text-right'],
    ];
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        {{-- Header --}}
        <x-page-header
            title="Gestión de Usuarios"
            subtitle="Administra los usuarios registrados en BgaGo"
            button-text="Nuevo Usuario"
        />

        {{-- Estadísticas --}}
        <x-dashboard.stats-grid>
            <x-stats.card
                title="Total Usuarios"
                :value="$stats['total']"
                icon="users"
                color="blue"
            />

            <x-stats.card
                title="Activos"
                :value="$stats['activos']"
                icon="check-circle"
                color="green"
            />

            <x-stats.card
                title="Nuevos (Mes)"
                :value="$stats['nuevos_mes']"
                icon="user-plus"
                color="purple"
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
                <x-filters.bar search-placeholder="Buscar por nombre o email...">
                    <flux:select placeholder="Estado" class="min-w-[140px]">
                        <option value="">Todos</option>
                        <option value="active">Activos</option>
                        <option value="inactive">Inactivos</option>
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
                    @foreach($users as $user)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                                    <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                        {{ strtoupper(substr($user['name'], 0, 2)) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $user['name'] }}
                                    </div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        Registro: {{ date('d/m/Y', strtotime($user['registered_at'])) }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm">
                                <div class="text-zinc-900 dark:text-zinc-100">{{ $user['email'] }}</div>
                                <div class="text-zinc-500 dark:text-zinc-400">{{ $user['phone'] }}</div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <x-badges.status 
                                :status="$user['status']"
                                :label="MockDataService::getStatusLabel($user['status'])"
                            />
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ $user['reservas_count'] }}
                                </span>
                                <span class="text-sm text-zinc-500 dark:text-zinc-400">reservas</span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ date('d/m/Y', strtotime($user['ultima_reserva'])) }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $user['sede'] }}
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
                    :current-count="count($users)" 
                    :total="$stats['total']"
                    label="usuarios"
                />
            </x-slot:pagination>
        </x-table.container>

    </div>
</x-layouts.app>