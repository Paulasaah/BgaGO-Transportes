@php
use App\Enums\VehicleType;
use App\Enums\VehicleStatus;
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Editar Vehículo</flux:heading>
                <flux:subheading>Actualiza la información de {{ $vehicle->placa }}</flux:subheading>
            </div>
            
            <flux:button :href="route('admin.vehicles.index')" variant="ghost" icon="arrow-left">
                Volver
            </flux:button>
        </div>

        <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Información Básica</flux:heading>
                
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <flux:input name="placa" label="Placa" value="{{ old('placa', $vehicle->placa) }}" required />
                        @error('placa')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:input name="marca" label="Marca" value="{{ old('marca', $vehicle->marca) }}" required />
                        @error('marca')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:input name="modelo" label="Modelo" value="{{ old('modelo', $vehicle->modelo) }}" required />
                        @error('modelo')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:input name="year" type="number" label="Año" value="{{ old('year', $vehicle->year) }}" required />
                        @error('year')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:select name="tipo" label="Tipo" required>
                            @foreach(VehicleType::cases() as $tipo)
                                <option value="{{ $tipo->value }}" {{ old('tipo', $vehicle->tipo->value) === $tipo->value ? 'selected' : '' }}>
                                    {{ $tipo->label() }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('tipo')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:input name="color" label="Color" value="{{ old('color', $vehicle->color) }}" />
                        @error('color')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:input name="capacidad" type="number" label="Capacidad" value="{{ old('capacidad', $vehicle->capacidad) }}" required />
                        @error('capacidad')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:select name="estado" label="Estado" required>
                            @foreach(VehicleStatus::cases() as $estado)
                                <option value="{{ $estado->value }}" {{ old('estado', $vehicle->estado->value) === $estado->value ? 'selected' : '' }}>
                                    {{ $estado->label() }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('estado')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:select name="sede_id" label="Sede">
                            <option value="">Sin asignar</option>
                            @foreach($sedes as $sede)
                                <option value="{{ $sede->id }}" {{ old('sede_id', $vehicle->sede_id) == $sede->id ? 'selected' : '' }}>
                                    {{ $sede->nombre }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('sede_id')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:select name="conductor_id" label="Conductor">
                            <option value="">Sin conductor</option>
                            @foreach($conductores as $conductor)
                                <option value="{{ $conductor->id }}" {{ old('conductor_id', $vehicle->conductor_id) == $conductor->id ? 'selected' : '' }}>
                                    {{ $conductor->name }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('conductor_id')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Características</flux:heading>
                
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="tiene_gps" value="1" {{ old('tiene_gps', $vehicle->tiene_gps) ? 'checked' : '' }} class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500" />
                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Tiene GPS / Rastreador</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="visible_catalogo" value="1" {{ old('visible_catalogo', $vehicle->visible_catalogo ?? true) ? 'checked' : '' }} class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500" />
                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Visible en Catálogo</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:button :href="route('admin.vehicles.index')" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary" icon="check">Actualizar Vehículo</flux:button>
            </div>
        </form>

    </div>
</x-layouts.app>
