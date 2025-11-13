@php
    $profile = $user->driverProfile;
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Perfil del Conductor</flux:heading>
                <flux:subheading>{{ $user->name }}</flux:subheading>
            </div>
            <div class="flex items-center gap-2">
                <flux:button href="{{ route('admin.drivers.edit', $user) }}" icon="pencil">Editar</flux:button>
                <form action="{{ route('admin.drivers.toggle-status', $user) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" data-flux-button="data-flux-button" class="relative items-center font-medium justify-center gap-2 whitespace-nowrap disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none justify-center h-8 text-sm rounded-md px-3 inline-flex bg-transparent hover:bg-zinc-800/5 dark:hover:bg-white/15 text-zinc-800 dark:text-white">
                        <svg class="shrink-0 [:where(&)]:size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8 1a.75.75 0 0 1 .75.75v6.5a.75.75 0 0 1-1.5 0v-6.5A.75.75 0 0 1 8 1ZM4.11 3.05a.75.75 0 0 1 0 1.06 5.5 5.5 0 1 0 7.78 0 .75.75 0 0 1 1.06-1.06 7 7 0 1 1-9.9 0 .75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ ($profile?->is_active ?? false) ? 'Desactivar' : 'Activar' }}</span>
                    </button>
                </form>
                <flux:button href="{{ route('admin.drivers.index') }}" variant="ghost" icon="arrow-left">Volver</flux:button>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="sm">Información General</flux:heading>
                <div class="mt-4 space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <div>Nombre<br><span class="font-medium text-zinc-900 dark:text-white">{{ $user->name }}</span></div>
                    <div>Correo<br><span class="font-medium">{{ $user->email }}</span></div>
                    <div>Fecha de registro<br><span class="font-medium">{{ $user->created_at->format('d/m/Y') }}</span></div>
                    <div>Estado<br>
                        @if($profile?->is_active)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Activo</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Inactivo</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="sm">Licencia de Conductor</flux:heading>
                <div class="mt-4 text-sm text-zinc-700 dark:text-zinc-300">
                    @if($profile?->license_number)
                        <div>Número<br><span class="font-mono font-medium">{{ $profile->license_number }}</span></div>
                        @if($profile->license_expiry)
                            <div class="mt-2">Vence<br><span class="font-medium">{{ \Carbon\Carbon::parse($profile->license_expiry)->format('d/m/Y') }}</span></div>
                        @endif
                    @else
                        <div class="flex items-center justify-center h-32 text-zinc-500 dark:text-zinc-400">No hay información de licencia registrada</div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="sm">Vehículo Asignado</flux:heading>
                <div class="mt-4 text-sm text-zinc-700 dark:text-zinc-300">
                    @if($assignedVehicle)
                        <div>Placa<br><span class="font-mono font-medium">{{ $assignedVehicle->placa }}</span></div>
                        <div class="mt-2">Marca<br><span class="font-medium">{{ $assignedVehicle->marca }}</span></div>
                        <div class="mt-2">Modelo<br><span class="font-medium">{{ $assignedVehicle->modelo }}</span></div>
                        <div class="mt-2">Año<br><span class="font-medium">{{ $assignedVehicle->year }}</span></div>
                        <div class="mt-2">Estado<br><span class="font-medium">{{ $assignedVehicle->estado->label() }}</span></div>
                        <div class="mt-3">
                            <flux:button href="{{ route('admin.vehicles.show', $assignedVehicle) }}" variant="ghost">Ver detalles del vehículo</flux:button>
                        </div>
                    @else
                        <div class="flex items-center justify-center h-32 text-zinc-500 dark:text-zinc-400">Sin vehículo asignado</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <flux:heading size="sm">Estadísticas de Rendimiento</flux:heading>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-stats.card title="Total de servicios" :value="$stats['total']" icon="chart-bar" color="orange" />
                <x-stats.card title="Completados" :value="$stats['completadas']" icon="check-badge" color="green" />
                <x-stats.card title="Cancelados" :value="$stats['canceladas']" icon="x-circle" color="red" />
                <x-stats.card title="En progreso" :value="$stats['activas']" icon="clock" color="blue" />
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <flux:heading size="sm">Servicios Recientes</flux:heading>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Vehículo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Monto</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($recentReservations as $res)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.reservations.show', $res) }}" class="font-mono text-sm font-medium hover:underline">{{ $res->codigo }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $res->user->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $res->vehicle?->placa ?? 'Sin asignar' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($res->estado->label()) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $res->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">${{ number_format($res->monto_final ?? 0, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <flux:button href="{{ route('admin.reservations.show', $res) }}" size="sm" variant="ghost" icon="eye" />
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-8 text-center text-zinc-500">Sin servicios recientes</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <flux:heading size="sm">Telemetría en Tiempo Real</flux:heading>
            <div class="mt-4">
                <livewire:map.map-view :filterType="'conductor'" />
            </div>
        </div>
    </div>
</x-layouts.app>