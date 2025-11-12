@php
use App\Enums\ReservationType;
use App\Enums\ReservationStatus;
@endphp

<x-layouts.app>
    <div class="flex flex-col gap-6 p-6 lg:p-8">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:button 
                    href="{{ route('admin.reservations.show', $reservation) }}" 
                    variant="ghost" 
                    icon="arrow-left"
                    size="sm"
                >
                    Volver
                </flux:button>
                <div>
                    <flux:heading size="xl">Editar Reserva</flux:heading>
                    <flux:subheading>
                        Código: <span class="font-mono">{{ $reservation->codigo }}</span>
                    </flux:subheading>
                </div>
            </div>
        </div>

        {{-- Formulario --}}
        <form action="{{ route('admin.reservations.update', $reservation) }}" method="POST" class="space-y-8">
            @csrf
            @method('PATCH')

            {{-- Información general --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Información General</flux:heading>

                <div class="grid gap-4 sm:grid-cols-2">
                    {{-- Cliente (no editable) --}}
                    <div>
                        <label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1 block">Cliente</label>
                        <input type="text" value="{{ $reservation->user->name }}" disabled
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 px-3 py-2">
                    </div>

                    {{-- Tipo de reserva --}}
                    <div>
                        <label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1 block">Tipo</label>
                        <select name="tipo" required
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-transparent px-3 py-2">
                            @foreach(ReservationType::cases() as $type)
                                <option value="{{ $type->value }}" 
                                    @selected($reservation->tipo === $type)>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Vehículo y sede --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Vehículo y Sede</flux:heading>

                <div class="grid gap-4 sm:grid-cols-2">
                    {{-- Vehículo --}}
                    <div>
                        <label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1 block">Vehículo</label>
                        <select name="vehiculo_id" required
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-transparent px-3 py-2">
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" 
                                    @selected($reservation->vehiculo_id === $vehicle->id)>
                                    {{ $vehicle->placa }} - {{ $vehicle->marca }} {{ $vehicle->modelo }}
                                    ({{ $vehicle->driver?->name ?? 'Sin conductor' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sede --}}
                    <div>
                        <label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1 block">Sede</label>
                        <select name="sede_id" required
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-transparent px-3 py-2">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" 
                                    @selected($reservation->sede_id === $branch->id)>
                                    {{ $branch->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Fechas --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Fechas y Horarios</flux:heading>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1 block">Inicio</label>
                        <input type="datetime-local" name="fecha_inicio" required
                            value="{{ $reservation->fecha_inicio->format('Y-m-d\TH:i') }}"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-transparent px-3 py-2">
                    </div>

                    <div>
                        <label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1 block">Fin</label>
                        <input type="datetime-local" name="fecha_fin" required
                            value="{{ $reservation->fecha_fin->format('Y-m-d\TH:i') }}"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-transparent px-3 py-2">
                    </div>
                </div>
            </div>

            {{-- Ubicación --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Ubicación</flux:heading>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1 block">Origen</label>
                        <input type="text" name="origen_direccion"
                            value="{{ $reservation->origen_direccion }}"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-transparent px-3 py-2">
                    </div>

                    <div>
                        <label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1 block">Destino</label>
                        <input type="text" name="destino_direccion"
                            value="{{ $reservation->destino_direccion }}"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-transparent px-3 py-2">
                    </div>
                </div>
            </div>

            {{-- Notas --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Notas del Cliente</flux:heading>
                <textarea name="notas_cliente" rows="4"
                    class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-transparent px-3 py-2"
                    placeholder="Detalles adicionales, instrucciones o comentarios">{{ $reservation->notas_cliente }}</textarea>
            </div>

            {{-- Acciones --}}
            <div class="flex justify-end">
                <flux:button type="submit" variant="primary" icon="check">
                    Guardar Cambios
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts.app>
