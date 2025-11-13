<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Gestión de Reservas</flux:heading>
            <flux:subheading>Visualiza y controla todas las reservas activas, completadas o canceladas</flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            <flux:button href="{{ route('admin.reservations.create') }}" icon="plus" variant="primary">
                Añadir reserva
            </flux:button>
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <x-stats.card title="Total Reservas" :value="$stats['total']" icon="clipboard-document-list" color="blue" />
        <x-stats.card title="Activas" :value="$stats['activas']" icon="clock" color="green" />
        <x-stats.card title="Completadas" :value="$stats['completadas']" icon="check-badge" color="purple" />
        <x-stats.card title="Canceladas" :value="$stats['canceladas']" icon="x-circle" color="red" />
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-4">
        <div class="flex flex-wrap gap-3">
            <flux:input placeholder="Buscar por código o usuario..." class="flex-1 min-w-[200px]" wire:model.live="search" />

            <flux:select placeholder="Tipo" class="min-w-[140px]" wire:model.live="tipo">
                <option value="">Todos</option>
                <option value="reserva">Reserva</option>
                <option value="domicilio">Domicilio</option>
            </flux:select>

            <flux:select placeholder="Estado" class="min-w-[150px]" wire:model.live="estado">
                <option value="">Todos</option>
                <option value="pendiente">Pendiente</option>
                <option value="confirmada">Confirmada</option>
                <option value="activa">Activa</option>
                <option value="completada">Completada</option>
                <option value="cancelada">Cancelada</option>
            </flux:select>

            <flux:input type="date" class="min-w-[140px]" wire:model.live="fecha" />

            <flux:button icon="magnifying-glass">Buscar</flux:button>
            <flux:button variant="ghost" wire:click="clearFilters">Limpiar</flux:button>
        </div>
    </div>

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
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Vehículo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Conductor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Monto</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach($reservations as $reserva)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $reserva->codigo }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">{{ $reserva->user->name ?? 'N/A' }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $reserva->user->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($reserva->vehicle)
                                    <span class="font-mono text-sm text-zinc-900 dark:text-zinc-100">{{ $reserva->vehicle->placa }}</span>
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-500 text-xs italic">Sin asignar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($reserva->driver)
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">{{ $reserva->driver->name }}</div>
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-500 text-xs italic">Sin asignar</span>
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
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">{{ $reserva->fecha_inicio->format('d/m/Y') }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $reserva->fecha_inicio->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs rounded-full font-medium
                                    @if($reserva->estado === \App\Enums\ReservationStatus::Activa)
                                        bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                    @elseif($reserva->estado === \App\Enums\ReservationStatus::Completada)
                                        bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($reserva->estado === \App\Enums\ReservationStatus::Pendiente || $reserva->estado === \App\Enums\ReservationStatus::Confirmada)
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
                                    <flux:button size="sm" variant="ghost" icon="eye" title="Ver detalles" href="{{ route('admin.reservations.show', $reserva) }}" class="text-zinc-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400" />
                                    <flux:button size="sm" variant="ghost" icon="pencil" title="Editar" href="{{ route('admin.reservations.edit', $reserva) }}" class="text-zinc-600 dark:text-zinc-400 hover:text-green-600 dark:hover:text-green-400" />
                                    @if($reserva->estado !== \App\Enums\ReservationStatus::Completada && $reserva->estado !== \App\Enums\ReservationStatus::Cancelada)
                                        <flux:button size="sm" variant="ghost" icon="x-mark" title="Cancelar reserva" wire:click="confirmDelete({{ $reserva->id }})" class="text-zinc-600 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200 dark:border-zinc-800">
                {{ $reservations->links() }}
            </div>
        @endif
    </div>
</div>