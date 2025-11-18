@php
use App\Enums\VehicleType;
use App\Enums\VehicleStatus;
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Crear Vehículo</flux:heading>
                <flux:subheading>Registra un nuevo vehículo en la flota</flux:subheading>
            </div>
            
            <flux:button :href="route('admin.vehicles.index')" variant="ghost" icon="arrow-left">
                Volver
            </flux:button>
        </div>

        {{-- Formulario --}}
        <form method="POST" action="{{ route('admin.vehicles.store') }}" class="space-y-6">
            @csrf

            {{-- Información Básica --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Información Básica</flux:heading>
                
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    {{-- Placa --}}
                    <div>
                        <flux:input 
                            name="placa" 
                            label="Placa" 
                            placeholder="ABC-123"
                            value="{{ old('placa') }}"
                            required
                        />
                        @error('placa')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>

                    {{-- Marca --}}
                    <div>
                        <flux:input 
                            name="marca" 
                            label="Marca" 
                            placeholder="Ej: Yamaha"
                            value="{{ old('marca') }}"
                            required
                        />
                        @error('marca')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>

                    {{-- Modelo --}}
                    <div>
                        <flux:input 
                            name="modelo" 
                            label="Modelo" 
                            placeholder="Ej: NMAX"
                            value="{{ old('modelo') }}"
                            required
                        />
                        @error('modelo')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>

                    {{-- Año --}}
                    <div>
                        <flux:input 
                            name="year" 
                            type="number"
                            label="Año" 
                            placeholder="{{ date('Y') }}"
                            value="{{ old('year', date('Y')) }}"
                            min="1900"
                            max="{{ date('Y') + 1 }}"
                            required
                        />
                        @error('year')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>

                    {{-- Tipo --}}
                    <div>
                        <flux:select name="tipo" label="Tipo de Vehículo" placeholder="Selecciona tipo" required>
                            @foreach(VehicleType::cases() as $tipo)
                                <option value="{{ $tipo->value }}" {{ old('tipo') === $tipo->value ? 'selected' : '' }}>
                                    {{ $tipo->label() }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('tipo')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>

                    {{-- Color --}}
                    <div>
                        <flux:input 
                            name="color" 
                            label="Color" 
                            placeholder="Ej: Negro"
                            value="{{ old('color') }}"
                        />
                        @error('color')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>

                    {{-- Capacidad --}}
                    <div>
                        <flux:input 
                            name="capacidad" 
                            type="number"
                            label="Capacidad (pasajeros)" 
                            placeholder="2"
                            value="{{ old('capacidad', 2) }}"
                            min="1"
                            max="100"
                            required
                        />
                        @error('capacidad')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>

                    {{-- Estado --}}
                    <div>
                        <flux:select name="estado" label="Estado" placeholder="Selecciona estado" required>
                            @foreach(VehicleStatus::cases() as $estado)
                                <option value="{{ $estado->value }}" {{ old('estado', 'disponible') === $estado->value ? 'selected' : '' }}>
                                    {{ $estado->label() }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('estado')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>

                    {{-- Sede --}}
                    <div>
                        <flux:select name="sede_id" label="Sede" placeholder="Selecciona sede">
                            <option value="">Sin asignar</option>
                            @foreach($sedes as $sede)
                                <option value="{{ $sede->id }}" {{ old('sede_id') == $sede->id ? 'selected' : '' }}>
                                    {{ $sede->nombre }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('sede_id')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Información Técnica --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Información Técnica</flux:heading>
                
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    {{-- Número de Motor --}}
                    <div>
                        <flux:input 
                            name="numero_motor" 
                            label="Número de Motor" 
                            placeholder="Opcional"
                            value="{{ old('numero_motor') }}"
                        />
                        @error('numero_motor')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>

                    {{-- Número de Chasis --}}
                    <div>
                        <flux:input 
                            name="numero_chasis" 
                            label="Número de Chasis" 
                            placeholder="Opcional"
                            value="{{ old('numero_chasis') }}"
                        />
                        @error('numero_chasis')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>

                    {{-- Kilometraje --}}
                    <div>
                        <flux:input 
                            name="kilometraje" 
                            type="number"
                            label="Kilometraje" 
                            placeholder="0"
                            value="{{ old('kilometraje', 0) }}"
                            min="0"
                        />
                        @error('kilometraje')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Conductor Asignado --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Asignación</flux:heading>
                
                <div class="grid gap-6 md:grid-cols-2">
                    {{-- Conductor --}}
                    <div>
                        <flux:select name="conductor_id" label="Conductor Asignado" placeholder="Sin asignar">
                            <option value="">Sin conductor</option>
                            @foreach($conductores as $conductor)
                                <option value="{{ $conductor->id }}" {{ old('conductor_id') == $conductor->id ? 'selected' : '' }}>
                                    {{ $conductor->name }} - {{ $conductor->email }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('conductor_id')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Características --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Características</flux:heading>
                
                <div class="grid gap-4 md:grid-cols-2">
                    {{-- GPS --}}
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="tiene_gps" 
                            value="1"
                            {{ old('tiene_gps', true) ? 'checked' : '' }}
                            class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500"
                        />
                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                            Tiene GPS / Rastreador
                        </span>
                    </label>

                    {{-- Visible en Catálogo --}}
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="visible_catalogo" 
                            value="1"
                            {{ old('visible_catalogo', true) ? 'checked' : '' }}
                            class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500"
                        />
                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                            Visible en Catálogo
                        </span>
                    </label>
                </div>
            </div>

            {{-- Observaciones --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Observaciones</flux:heading>
                
                <div>
                    <flux:textarea 
                        name="observaciones" 
                        label="Observaciones" 
                        placeholder="Notas adicionales sobre el vehículo..."
                        rows="4"
                    >{{ old('observaciones') }}</flux:textarea>
                    @error('observaciones')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3 bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:button 
                    :href="route('admin.vehicles.index')" 
                    variant="ghost"
                >
                    Cancelar
                </flux:button>
                
                <flux:button 
                    type="submit" 
                    variant="primary"
                    icon="check"
                >
                    Crear Vehículo
                </flux:button>
            </div>
        </form>

    </div>
</x-layouts.app>