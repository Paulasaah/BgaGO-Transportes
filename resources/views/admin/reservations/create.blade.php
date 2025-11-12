@php
use App\Enums\ReservationType;
@endphp

<x-layouts.app>
    <div class="flex flex-col gap-6 p-6 lg:p-8">

        {{-- Header --}}
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
                    <flux:heading size="xl">Nueva Reserva</flux:heading>
                    <flux:subheading>Completa la información para crear una reserva.</flux:subheading>
                </div>
            </div>
        </div>

        {{-- Formulario --}}
        <form action="{{ route('admin.reservations.store') }}" method="POST" class="space-y-8">
            @csrf

            {{-- Información básica --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Información Básica</flux:heading>

                <div class="grid gap-4 sm:grid-cols-2">
                    {{-- Cliente --}}
                    <div>
                        <label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1 block">Cliente</label>
                        <select name="user_id" required
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-transparent px-3 py-2">
                            <option value="">Seleccionar cliente</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tipo --}}
                    <div>
                        <label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1 block">Tipo de reserva</label>
                        <select name="tipo" required
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-transparent px-3 py-2">
                            @foreach(ReservationType::cases() as $type)
                                <option value="{{ $type->value }}">{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Selección de vehículo y sede --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Vehículo y Sede</flux:heading>

                <div class="grid gap-4 sm:grid-cols-2">
                    {{-- Vehículo --}}
                    <div>
                        <label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1 block">Vehículo</label>
                        <select name="vehiculo_id" required
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-transparent px-3 py-2">
                            <option value="">Seleccionar vehículo</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">
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
                            <option value="">Seleccionar sede</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->nombre }}</option>
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
                        <label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1 block">Fecha y hora de inicio</label>
                        <input type="datetime-local" name="fecha_inicio" required
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-transparent px-3 py-2">
                    </div>

                    <div>
                        <label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1 block">Fecha y hora de fin</label>
                        <input type="datetime-local" name="fecha_fin" required
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-transparent px-3 py-2">
                    </div>
                </div>
            </div>

            {{-- Ubicación --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Ubicación</flux:heading>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1 block">Dirección de origen</label>
                        <input type="text" name="origen_direccion"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-transparent px-3 py-2"
                            placeholder="Ej: Calle 45 #12-30">
                    </div>

                    <div>
                        <label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1 block">Dirección de destino</label>
                        <input type="text" name="destino_direccion"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-transparent px-3 py-2"
                            placeholder="Ej: Carrera 27 #14-50">
                    </div>
                </div>
            </div>

            {{-- Notas --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Notas del Cliente</flux:heading>
                <textarea name="notas_cliente" rows="4"
                    class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-transparent px-3 py-2"
                    placeholder="Detalles adicionales, instrucciones o comentarios"></textarea>
            </div>

            {{-- Acciones --}}
            <div class="flex justify-end">
                <flux:button type="submit" variant="primary" icon="plus-circle">
                    Crear Reserva
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts.app>
