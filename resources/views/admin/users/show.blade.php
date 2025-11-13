<x-layouts.app>
    @php
        $roleName = $user->roles->first()->name ?? null;
    @endphp
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Perfil del Usuario</flux:heading>
                <flux:subheading>{{ $user->name }}</flux:subheading>
            </div>
            <div class="flex items-center gap-2">
                <flux:button href="{{ route('admin.users.index') }}" variant="ghost" icon="arrow-left" wire:navigate>Volver</flux:button>
                <flux:button href="{{ route('admin.users.edit', $user) }}" variant="outline" icon="pencil" wire:navigate>Editar</flux:button>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6">
                <div class="mb-4 text-sm font-semibold text-zinc-400">Información General</div>
                <div class="flex items-center gap-4 mb-4">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                        <span class="text-white font-semibold text-sm">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">ID: {{ $user->id }}</div>
                    </div>
                </div>
                <div class="grid gap-3">
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Correo</div>
                        <div class="text-sm text-zinc-900 dark:text-zinc-100">{{ $user->email }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Fecha de registro</div>
                        <div class="text-sm text-zinc-900 dark:text-zinc-100">{{ $user->created_at->format('d/m/Y') }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Rol</div>
                        <div>
                            @if($roleName)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium
                                    @if($roleName === 'admin')
                                        bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400
                                    @elseif($roleName === 'conductor')
                                        bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400
                                    @else
                                        bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                                    @endif
                                ">{{ ucfirst($roleName) }}</span>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-500 text-xs italic">Sin rol</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6">
                <div class="mb-4 text-sm font-semibold text-zinc-400">Estadísticas</div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <div class="text-xs text-zinc-500">Total de reservas</div>
                        <div class="text-2xl font-semibold">{{ $stats['total'] ?? 0 }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-zinc-500">Completadas</div>
                        <div class="text-2xl font-semibold text-green-600">{{ $stats['completadas'] ?? 0 }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-zinc-500">Canceladas</div>
                        <div class="text-2xl font-semibold text-red-600">{{ $stats['canceladas'] ?? 0 }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-zinc-500">Pagos registrados</div>
                        <div class="text-2xl font-semibold">{{ $paymentsCount ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6">
                <div class="mb-4 text-sm font-semibold text-zinc-400">Actividad Reciente</div>
                @if($lastReservation)
                    <div class="grid gap-2">
                        <div class="text-sm text-zinc-900 dark:text-zinc-100">Reserva #{{ $lastReservation->codigo }}</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $lastReservation->fecha_inicio?->format('d/m/Y H:i') }}</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Estado: {{ $lastReservation->estado->value }}</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Vehículo: {{ $lastReservation->vehicle?->placa ?? '—' }}</div>
                    </div>
                @else
                    <div class="text-sm text-zinc-500 dark:text-zinc-400">No tiene reservas recientes.</div>
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6">
            <div class="mb-4 flex items-center justify-between">
                <div class="text-sm font-semibold text-zinc-400">Reservas del Usuario</div>
            </div>
            @if($recentReservations->isEmpty())
                <div class="flex items-center justify-center py-12 text-sm text-zinc-500">
                    Este usuario no tiene reservas.
                </div>
            @else
                <div class="grid gap-2">
                    @foreach($recentReservations as $res)
                        <div class="flex items-center justify-between rounded-lg border border-zinc-200 dark:border-zinc-800 p-3">
                            <div class="text-sm">#{{ $res->codigo }} · {{ $res->estado->value }} · {{ $res->fecha_inicio?->format('d/m/Y H:i') }}</div>
                            <div class="text-xs text-zinc-500">{{ $res->vehicle?->placa ?? 'Sin vehículo' }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6">
            <div class="mb-4 text-sm font-semibold text-zinc-400">Pagos Realizados</div>
            @if($recentPayments->isEmpty())
                <div class="flex items-center justify-center py-12 text-sm text-zinc-500">
                    Este usuario no tiene pagos registrados.
                </div>
            @else
                <div class="grid gap-2">
                    @foreach($recentPayments as $pay)
                        <div class="flex items-center justify-between rounded-lg border border-zinc-200 dark:border-zinc-800 p-3">
                            <div class="text-sm">Pago #{{ $pay->id }} · ${{ number_format($pay->monto, 0, ',', '.') }}</div>
                            <div class="text-xs text-zinc-500">{{ $pay->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>