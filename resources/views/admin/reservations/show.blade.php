@php
use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">

        {{-- Header con acciones --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:button 
                    href="{{ route('admin.reservations.index') }}" 
                    variant="ghost" 
                    icon="arrow-left"
                    size="sm"
                >
                    Volver
                </flux:button>
                <div>
                    <flux:heading size="xl">Detalle de Reserva</flux:heading>
                    <flux:subheading>Código: <span class="font-mono">{{ $reservation->codigo }}</span></flux:subheading>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($reservation->estado === ReservationStatus::Pendiente)
                    <form action="{{ route('admin.reservations.confirm', $reservation) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <flux:button type="submit" variant="primary" icon="check">
                            Confirmar
                        </flux:button>
                    </form>

                    <flux:button 
                        href="{{ route('admin.reservations.edit', $reservation) }}" 
                        icon="pencil"
                    >
                        Editar
                    </flux:button>
                @endif

                @if(!in_array($reservation->estado, [ReservationStatus::Completada, ReservationStatus::Cancelada]))
                    <form action="{{ route('admin.reservations.cancel', $reservation) }}" method="POST" 
                          onsubmit="return confirm('¿Estás seguro de cancelar esta reserva?')">
                        @csrf
                        @method('PATCH')
                        <flux:button type="submit" variant="danger" icon="x-mark">
                            Cancelar
                        </flux:button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Estado y tipo --}}
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-zinc-600 dark:text-zinc-400">Estado:</span>
                @if($reservation->estado === ReservationStatus::Activa)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-full font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                        <span class="size-2 rounded-full bg-blue-500 animate-pulse"></span>
                        Activa
                    </span>
                @elseif($reservation->estado === ReservationStatus::Completada)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-full font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                        <flux:icon.check-circle class="size-4" />
                        Completada
                    </span>
                @elseif($reservation->estado === ReservationStatus::Pendiente)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-full font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                        <flux:icon.clock class="size-4" />
                        Pendiente
                    </span>
                @elseif($reservation->estado === ReservationStatus::Confirmada)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-full font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                        <flux:icon.check-badge class="size-4" />
                        Confirmada
                    </span>
                @elseif($reservation->estado === ReservationStatus::Cancelada)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-full font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                        <flux:icon.x-circle class="size-4" />
                        Cancelada
                    </span>
                @endif
            </div>

            <div class="h-6 w-px bg-zinc-300 dark:bg-zinc-700"></div>

            <div class="flex items-center gap-2">
                <span class="text-sm text-zinc-600 dark:text-zinc-400">Tipo:</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-sm font-medium
                    @if($reservation->tipo === ReservationType::Reserva)
                        bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                    @else
                        bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400
                    @endif
                ">
                    <flux:icon.{{ $reservation->tipo === ReservationType::Reserva ? 'calendar' : 'truck' }} class="size-4" />
                    {{ ucfirst($reservation->tipo->value) }}
                </span>
            </div>
        </div>

        {{-- Grid de información --}}
        <div class="grid gap-6 lg:grid-cols-3">
            
            {{-- Información del Cliente --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="size-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <flux:icon.user class="size-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <flux:heading size="lg">Cliente</flux:heading>
                </div>

                <div class="space-y-3">
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Nombre</div>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->user->name }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Email</div>
                        <div class="text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->user->email }}
                        </div>
                    </div>

                    @if($reservation->user->phone)
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Teléfono</div>
                        <div class="text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->user->phone }}
                        </div>
                    </div>
                    @endif

                    <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800">
                        <flux:button 
                            href="{{ route('admin.users.show', $reservation->user) }}" 
                            variant="ghost" 
                            size="sm"
                            class="w-full"
                        >
                            Ver perfil completo
                        </flux:button>
                    </div>
                </div>
            </div>

            {{-- Información del Vehículo --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="size-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <flux:icon.truck class="size-5 text-green-600 dark:text-green-400" />
                    </div>
                    <flux:heading size="lg">Vehículo</flux:heading>
                </div>

                @if($reservation->vehicle)
                <div class="space-y-3">
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Placa</div>
                        <div class="font-mono font-bold text-lg text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->vehicle->placa }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Vehículo</div>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->vehicle->marca }} {{ $reservation->vehicle->modelo }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Tipo</div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-sm">
                            {{ $reservation->vehicle->tipo->label() }}
                        </span>
                    </div>

                    <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800">
                        <flux:button 
                            href="{{ route('admin.vehicles.show', $reservation->vehicle) }}" 
                            variant="ghost" 
                            size="sm"
                            class="w-full"
                        >
                            Ver detalles del vehículo
                        </flux:button>
                    </div>
                </div>
                @else
                <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                    <flux:icon.exclamation-triangle class="size-8 mx-auto mb-2 opacity-50" />
                    <p class="text-sm">Sin vehículo asignado</p>
                </div>
                @endif
            </div>

            {{-- Información del Conductor --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="size-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                        <flux:icon.user-circle class="size-5 text-orange-600 dark:text-orange-400" />
                    </div>
                    <flux:heading size="lg">Conductor</flux:heading>
                </div>

                @if($reservation->driver)
                <div class="space-y-3">
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Nombre</div>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->driver->name }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Email</div>
                        <div class="text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->driver->email }}
                        </div>
                    </div>

                    @if($reservation->driver->phone)
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Teléfono</div>
                        <div class="text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->driver->phone }}
                        </div>
                    </div>
                    @endif

                    @if($reservation->driver->driverProfile?->license_number)
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Licencia</div>
                        <div class="font-mono text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->driver->driverProfile->license_number }}
                        </div>
                    </div>
                    @endif
                </div>
                @else
                <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                    <flux:icon.exclamation-triangle class="size-8 mx-auto mb-2 opacity-50" />
                    <p class="text-sm">Sin conductor asignado</p>
                </div>
                @endif
            </div>

        </div>

        {{-- Fechas y ubicación --}}
        <div class="grid gap-6 lg:grid-cols-2">
            
            {{-- Fechas --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="size-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <flux:icon.calendar class="size-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <flux:heading size="lg">Fechas y Horarios</flux:heading>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Inicio</div>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->fecha_inicio->format('d/m/Y') }}
                        </div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $reservation->fecha_inicio->format('h:i A') }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Fin</div>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->fecha_fin->format('d/m/Y') }}
                        </div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $reservation->fecha_fin->format('h:i A') }}
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Duración</div>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->fecha_inicio->diffForHumans($reservation->fecha_fin, true) }}
                        </div>
                    </div>

                    @if($reservation->fecha_inicio_real)
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Inicio Real</div>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->fecha_inicio_real->format('d/m/Y h:i A') }}
                        </div>
                    </div>
                    @endif

                    @if($reservation->fecha_fin_real)
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Fin Real</div>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->fecha_fin_real->format('d/m/Y h:i A') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Ubicación --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="size-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <flux:icon.map-pin class="size-5 text-red-600 dark:text-red-400" />
                    </div>
                    <flux:heading size="lg">Ubicación</flux:heading>
                </div>

                <div class="space-y-4">
                    @if($reservation->branch)
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Sede</div>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->branch->nombre }}
                        </div>
                        @if($reservation->branch->direccion)
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                            {{ $reservation->branch->direccion }}
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($reservation->origen_direccion)
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Origen</div>
                        <div class="text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->origen_direccion }}
                        </div>
                    </div>
                    @endif

                    @if($reservation->destino_direccion)
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Destino</div>
                        <div class="text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $reservation->destino_direccion }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Información de pago --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="size-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <flux:icon.currency-dollar class="size-5 text-green-600 dark:text-green-400" />
                </div>
                <flux:heading size="lg">Información de Pago</flux:heading>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Subtotal</div>
                    <div class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                        ${{ number_format($reservation->monto_base ?? 0, 0, ',', '.') }}
                    </div>
                </div>

                @if($reservation->descuento > 0)
                <div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Descuento</div>
                    <div class="text-xl font-bold text-red-600 dark:text-red-400">
                        -${{ number_format($reservation->descuento, 0, ',', '.') }}
                    </div>
                </div>
                @endif

                <div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Total Final</div>
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        ${{ number_format($reservation->monto_final ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            @if($reservation->payments->isNotEmpty())
            <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-3">Historial de Pagos</div>
                <div class="space-y-2">
                    @foreach($reservation->payments as $payment)
                    <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ ucfirst($payment->metodo_pago) }}
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $payment->created_at->format('d/m/Y h:i A') }}
                            </div>
                        </div>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                            ${{ number_format($payment->monto, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Notas adicionales --}}
        @if($reservation->notas_cliente || $reservation->motivo_cancelacion)
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="size-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <flux:icon.document-text class="size-5 text-zinc-600 dark:text-zinc-400" />
                </div>
                <flux:heading size="lg">Notas</flux:heading>
            </div>

            @if($reservation->notas_cliente)
            <div class="mb-4">
                <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Notas del Cliente</div>
                <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg text-sm text-zinc-900 dark:text-zinc-100">
                    {{ $reservation->notas_cliente }}
                </div>
            </div>
            @endif

            @if($reservation->motivo_cancelacion)
            <div>
                <div class="text-sm font-medium text-red-700 dark:text-red-400 mb-2">Motivo de Cancelación</div>
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg text-sm text-zinc-900 dark:text-zinc-100">
                    {{ $reservation->motivo_cancelacion }}
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Timeline de eventos --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="size-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <flux:icon.clock class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
                <flux:heading size="lg">Timeline de Eventos</flux:heading>
            </div>

            <div class="space-y-4">
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="size-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <flux:icon.plus class="size-4 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="w-0.5 flex-1 bg-zinc-200 dark:bg-zinc-800 mt-2"></div>
                    </div>
                    <div class="flex-1 pb-6">
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Reserva Creada</div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $reservation->created_at->format('d/m/Y h:i A') }}
                        </div>
                    </div>
                </div>

                @if($reservation->estado !== ReservationStatus::Pendiente)
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="size-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <flux:icon.check class="size-4 text-green-600 dark:text-green-400" />
                        </div>
                        @if($reservation->estado !== ReservationStatus::Confirmada)
                        <div class="w-0.5 flex-1 bg-zinc-200 dark:bg-zinc-800 mt-2"></div>
                        @endif
                    </div>
                    <div class="flex-1 pb-6">
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Confirmada</div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                            Estado actualizado a confirmada
                        </div>
                    </div>
                </div>
                @endif

                @if($reservation->fecha_inicio_real)
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="size-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <flux:icon.play class="size-4 text-blue-600 dark:text-blue-400" />
                        </div>
                        @if(!$reservation->fecha_fin_real)
                        <div class="w-0.5 flex-1 bg-zinc-200 dark:bg-zinc-800 mt-2"></div>
                        @endif
                    </div>
                    <div class="flex-1 pb-6">
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Iniciada</div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $reservation->fecha_inicio_real->format('d/m/Y h:i A') }}
                        </div>
                    </div>
                </div>
                @endif

                @if($reservation->fecha_fin_real)
                <div class="flex gap-4">
                    <div class="size-8 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <flux:icon.check-circle class="size-4 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Completada</div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $reservation->fecha_fin_real->format('d/m/Y h:i A') }}
                        </div>
                    </div>
                </div>
                @endif

                @if($reservation->estado === ReservationStatus::Cancelada)
                <div class="flex gap-4">
                    <div class="size-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <flux:icon.x-mark class="size-4 text-red-600 dark:text-red-400" />
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Cancelada</div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $reservation->fecha_cancelacion ? $reservation->fecha_cancelacion->format('d/m/Y h:i A') : 'Fecha no disponible' }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>
</x-layouts.app>