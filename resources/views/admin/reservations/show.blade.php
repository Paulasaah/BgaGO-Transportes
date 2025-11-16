@php
use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="size-16 rounded-lg bg-gradient-to-br 
                    @if($reservation->tipo === ReservationType::Reserva)
                        from-blue-500 to-blue-600
                    @else
                        from-purple-500 to-purple-600
                    @endif
                    flex items-center justify-center">
                    @if($reservation->tipo === ReservationType::Reserva)
                        <flux:icon.clipboard-document-list class="size-8 text-white" />
                    @else
                        <flux:icon.truck class="size-8 text-white" />
                    @endif
                </div>
                
                <div>
                    <flux:heading size="xl">{{ $reservation->codigo }}</flux:heading>
                    <flux:subheading>{{ $reservation->tipo->label() }} - {{ $reservation->estado->label() }}</flux:subheading>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                @if($reservation->estado !== ReservationStatus::Completada && $reservation->estado !== ReservationStatus::Cancelada)
                    <form method="POST" action="{{ route('admin.reservations.cancel', $reservation) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <flux:button 
                            type="submit" 
                            variant="danger" 
                            icon="x-mark"
                            onclick="return confirm('¿Estás seguro de cancelar esta reserva?')"
                        >
                            Cancelar Reserva
                        </flux:button>
                    </form>
                @endif
                
                <flux:button :href="route('admin.reservations.index')" variant="ghost" icon="arrow-left">
                    Volver
                </flux:button>
            </div>
        </div>

        {{-- Estado y Timeline --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <flux:heading size="lg" class="mb-4">Estado Actual</flux:heading>
            
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium
                    @if($reservation->estado === ReservationStatus::Pendiente)
                        bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                    @elseif($reservation->estado === ReservationStatus::Confirmada)
                        bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                    @elseif($reservation->estado === ReservationStatus::Activa)
                        bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                    @elseif($reservation->estado === ReservationStatus::Completada)
                        bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400
                    @else
                        bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                    @endif
                ">
                    <span class="size-2 rounded-full bg-current animate-pulse"></span>
                    {{ $reservation->estado->label() }}
                </span>
                
                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                    Creada: {{ $reservation->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Información Principal --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Cliente --}}
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Información del Cliente</flux:heading>
                    
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="size-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-semibold text-lg">
                                    {{ strtoupper(substr($reservation->user->name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $reservation->user->name }}
                                </div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $reservation->user->email }}
                                </div>
                                @if($reservation->user->phone)
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        📱 {{ $reservation->user->phone }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fechas y Horarios --}}
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Fechas y Horarios</flux:heading>
                    
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Inicio</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $reservation->fecha_inicio->format('d/m/Y') }}
                            </div>
                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $reservation->fecha_inicio->format('H:i') }}
                            </div>
                        </div>
                        
                        @if($reservation->fecha_fin)
                        <div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Fin</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $reservation->fecha_fin->format('d/m/Y') }}
                            </div>
                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $reservation->fecha_fin->format('H:i') }}
                            </div>
                        </div>
                        @endif
                        
                        @php
                            $duracion = $reservation->fecha_inicio->diff($reservation->fecha_fin ?? now());
                        @endphp
                        <div class="md:col-span-2">
                            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Duración</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                @if($duracion->days > 0)
                                    {{ $duracion->days }} día(s)
                                @endif
                                {{ $duracion->h }} hora(s) {{ $duracion->i }} minuto(s)
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ubicaciones (solo para domicilios) --}}
                @if($reservation->tipo === ReservationType::Domicilio)
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Ubicaciones</flux:heading>
                    
                    <div class="space-y-4">
                        @if($reservation->direccion_origen)
                        <div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">📍 Origen</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $reservation->direccion_origen }}
                            </div>
                        </div>
                        @endif
                        
                        @if($reservation->direccion_destino)
                        <div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">🎯 Destino</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $reservation->direccion_destino }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Observaciones --}}
                @if($reservation->observaciones)
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Observaciones</flux:heading>
                    <p class="text-zinc-700 dark:text-zinc-300">{{ $reservation->observaciones }}</p>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                
                {{-- Vehículo --}}
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Vehículo</flux:heading>
                    
                    @if($reservation->vehicle)
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <flux:icon.truck class="size-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <div class="font-mono font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $reservation->vehicle->placa }}
                                    </div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $reservation->vehicle->marca }} {{ $reservation->vehicle->modelo }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-sm">
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                                    {{ $reservation->vehicle->tipo->label() }}
                                </span>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 italic">Sin vehículo asignado</p>
                    @endif
                </div>

                {{-- Conductor --}}
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Conductor</flux:heading>
                    
                    @if($reservation->driver)
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <span class="text-green-600 dark:text-green-400 font-semibold text-sm">
                                    {{ strtoupper(substr($reservation->driver->name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $reservation->driver->name }}
                                </div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $reservation->driver->email }}
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 italic">Sin conductor asignado</p>
                    @endif
                </div>

                {{-- Sede --}}
                @if($reservation->branch)
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Sede</flux:heading>
                    
                    <div class="font-medium text-zinc-900 dark:text-zinc-100">
                        {{ $reservation->branch->nombre }}
                    </div>
                    @if($reservation->branch->direccion)
                        <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                            {{ $reservation->branch->direccion }}
                        </div>
                    @endif
                </div>
                @endif

                {{-- Monto --}}
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Monto</flux:heading>
                    
                    <div class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                        ${{ number_format($reservation->monto_final ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        COP
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
