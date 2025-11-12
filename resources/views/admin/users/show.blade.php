@php
use App\Enums\ReservationStatus;
use App\Enums\PaymentStatus;
@endphp

<x-layouts.app>
    <div class="flex flex-col gap-6 p-6 lg:p-8">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:button 
                    href="{{ route('admin.users.index') }}" 
                    variant="ghost" 
                    icon="arrow-left"
                    size="sm"
                >
                    Volver
                </flux:button>

                <div>
                    <flux:heading size="xl">Perfil del Usuario</flux:heading>
                    <flux:subheading>{{ $user->name }}</flux:subheading>
                </div>
            </div>

            <div class="flex gap-2">
                <flux:button href="{{ route('admin.users.edit', $user) }}" icon="pencil" variant="primary">
                    Editar
                </flux:button>
            </div>
        </div>

        {{-- Información principal --}}
        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Información general --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Información General</flux:heading>

                <div class="space-y-3">
                    <x-info-row label="Nombre" :value="$user->name" />
                    <x-info-row label="Correo" :value="$user->email" />
                    @if($user->phone)
                        <x-info-row label="Teléfono" :value="$user->phone" />
                    @endif
                    @if($user->created_at)
                        <x-info-row label="Fecha de registro" :value="$user->created_at->format('d/m/Y')" />
                    @endif

                    <div class="pt-3">
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Roles</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->roles as $role)
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Estadísticas --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Estadísticas</flux:heading>

                <div class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Total de reservas</div>
                        <div class="text-2xl font-bold">{{ $user->reservations->count() }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Completadas</div>
                        <div class="text-2xl font-bold text-green-600">
                            {{ $user->reservations->where('estado', ReservationStatus::Completada)->count() }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Canceladas</div>
                        <div class="text-2xl font-bold text-red-500">
                            {{ $user->reservations->where('estado', ReservationStatus::Cancelada)->count() }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Pagos registrados</div>
                        <div class="text-2xl font-bold text-amber-500">
                            {{ $user->payments->count() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actividad reciente --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Actividad Reciente</flux:heading>

                @php
                    $lastReservation = $user->reservations->sortByDesc('created_at')->first();
                @endphp

                @if($lastReservation)
                    <x-info-row label="Última reserva" :value="$lastReservation->codigo" />
                    <x-info-row label="Estado" :value="$lastReservation->estado->label()" />
                    <x-info-row label="Inicio" :value="$lastReservation->fecha_inicio?->format('d/m/Y H:i')" />
                    <x-info-row label="Fin" :value="$lastReservation->fecha_fin?->format('d/m/Y H:i')" />
                @else
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">No tiene reservas recientes.</p>
                @endif
            </div>
        </div>

        {{-- Reservas --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="flex items-center gap-3 mb-4">
                <flux:icon.calendar class="size-5 text-blue-600 dark:text-blue-400" />
                <flux:heading size="lg">Reservas del Usuario</flux:heading>
            </div>

            @if($user->reservations->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-zinc-600 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-800">
                            <tr>
                                <th class="text-left py-2">Código</th>
                                <th class="text-left py-2">Vehículo</th>
                                <th class="text-left py-2">Estado</th>
                                <th class="text-left py-2">Inicio</th>
                                <th class="text-left py-2">Fin</th>
                                <th class="text-left py-2">Monto Final</th>
                                <th class="text-left py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @foreach($user->reservations as $r)
                                <tr>
                                    <td class="py-2 font-mono text-blue-600 dark:text-blue-400">{{ $r->codigo }}</td>
                                    <td class="py-2">{{ $r->vehicle?->marca }} {{ $r->vehicle?->modelo }}</td>
                                    <td class="py-2">{{ $r->estado->label() }}</td>
                                    <td class="py-2">{{ $r->fecha_inicio?->format('d/m/Y H:i') }}</td>
                                    <td class="py-2">{{ $r->fecha_fin?->format('d/m/Y H:i') }}</td>
                                    <td class="py-2">${{ number_format($r->monto_final, 0, ',', '.') }}</td>
                                    <td class="py-2">
                                        <flux:button
                                            href="{{ route('admin.reservations.show', $r) }}"
                                            size="xs"
                                            icon="eye"
                                            variant="ghost"
                                        />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                    <flux:icon.inbox class="size-8 mx-auto mb-2 opacity-50" />
                    <p class="text-sm">Este usuario no tiene reservas.</p>
                </div>
            @endif
        </div>

        {{-- Pagos --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="flex items-center gap-3 mb-4">
                <flux:icon.credit-card class="size-5 text-amber-600 dark:text-amber-400" />
                <flux:heading size="lg">Pagos Realizados</flux:heading>
            </div>

            @if($user->payments->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-zinc-600 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-800">
                            <tr>
                                <th class="text-left py-2">ID</th>
                                <th class="text-left py-2">Reserva</th>
                                <th class="text-left py-2">Monto</th>
                                <th class="text-left py-2">Estado</th>
                                <th class="text-left py-2">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @foreach($user->payments as $p)
                                <tr>
                                    <td class="py-2">{{ $p->id }}</td>
                                    <td class="py-2 font-mono text-blue-600 dark:text-blue-400">{{ $p->reservation?->codigo ?? 'N/A' }}</td>
                                    <td class="py-2">${{ number_format($p->monto, 0, ',', '.') }}</td>
                                    <td class="py-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @class([
                                                'bg-green-100 text-green-700' => $p->estado?->value === 'aprobado',
                                                'bg-yellow-100 text-yellow-700' => $p->estado?->value === 'pendiente',
                                                'bg-red-100 text-red-700' => $p->estado?->value === 'rechazado',
                                            ])
                                        ">
                                            {{ ucfirst($p->estado?->value ?? 'Desconocido') }}
                                        </span>
                                    </td>
                                    <td class="py-2">{{ $p->created_at?->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                    <flux:icon.wallet class="size-8 mx-auto mb-2 opacity-50" />
                    <p class="text-sm">Este usuario no tiene pagos registrados.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
