@php
use App\Enums\MaintenanceType;
use App\Enums\MaintenanceStatus;
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Registrar Mantenimiento</flux:heading>
                <flux:subheading>Registra un nuevo mantenimiento preventivo o correctivo</flux:subheading>
            </div>
            
            <flux:button :href="route('admin.maintenances.index')" variant="ghost" icon="arrow-left">
                Volver
            </flux:button>
        </div>

        <form method="POST" action="{{ route('admin.maintenances.store') }}" class="space-y-6">
            @csrf

            {{-- Información del Vehículo --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Vehículo y Tipo de Mantenimiento</flux:heading>
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <flux:select name="vehiculo_id" label="Vehículo" placeholder="Selecciona un vehículo" required>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehiculo_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->placa }} - {{ $vehicle->marca }} {{ $vehicle->modelo }} ({{ $vehicle->tipo->label() }})
                                </option>
                            @endforeach
                        </flux:select>
                        @error('vehiculo_id')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:select name="tipo" label="Tipo de Mantenimiento" placeholder="Selecciona tipo" required>
                            @foreach(MaintenanceType::cases() as $tipo)
                                <option value="{{ $tipo->value }}" {{ old('tipo') === $tipo->value ? 'selected' : '' }}>
                                    {{ $tipo->label() }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('tipo')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:select name="estado" label="Estado" placeholder="Selecciona estado">
                            @foreach(MaintenanceStatus::cases() as $estado)
                                <option value="{{ $estado->value }}" {{ old('estado', 'programado') === $estado->value ? 'selected' : '' }}>
                                    {{ $estado->label() }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('estado')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>
                </div>
            </div>

            {{-- Fechas --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Fechas</flux:heading>
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <flux:input 
                            type="date" 
                            name="fecha_programada" 
                            label="Fecha Programada" 
                            value="{{ old('fecha_programada', now()->format('Y-m-d')) }}"
                        />
                        @error('fecha_programada')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:input 
                            type="date" 
                            name="fecha_realizada" 
                            label="Fecha Realizada (opcional)" 
                            value="{{ old('fecha_realizada') }}"
                        />
                        @error('fecha_realizada')<flux:error>{{ $message }}</flux:error>@enderror
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Solo si ya fue realizado</p>
                    </div>
                </div>
            </div>

            {{-- Kilometraje --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Control de Kilometraje</flux:heading>
                
                <div class="grid gap-6 md:grid-cols-3">
                    <div>
                        <flux:input 
                            type="number" 
                            name="kilometraje_actual" 
                            label="Kilometraje Actual" 
                            placeholder="0"
                            value="{{ old('kilometraje_actual') }}"
                            min="0"
                        />
                        @error('kilometraje_actual')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:input 
                            type="number" 
                            name="kilometraje_proximo" 
                            label="Próximo Mantenimiento (km)" 
                            placeholder="Se calcula automáticamente"
                            value="{{ old('kilometraje_proximo') }}"
                            min="0"
                        />
                        @error('kilometraje_proximo')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:input 
                            type="number" 
                            name="kilometraje_intervalo" 
                            label="Intervalo (km)" 
                            placeholder="Ej: 5000"
                            value="{{ old('kilometraje_intervalo') }}"
                            min="0"
                        />
                        @error('kilometraje_intervalo')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>
                </div>
            </div>

            {{-- Descripción y Detalles --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Descripción y Detalles</flux:heading>
                
                <div class="space-y-4">
                    <div>
                        <flux:textarea 
                            name="descripcion" 
                            label="Descripción del Mantenimiento" 
                            placeholder="Describe el trabajo realizado o a realizar..."
                            rows="4"
                            required
                        >{{ old('descripcion') }}</flux:textarea>
                        @error('descripcion')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <flux:input 
                                name="taller" 
                                label="Taller / Proveedor" 
                                placeholder="Nombre del taller"
                                value="{{ old('taller') }}"
                            />
                            @error('taller')<flux:error>{{ $message }}</flux:error>@enderror
                        </div>

                        <div>
                            <flux:input 
                                name="mecanico" 
                                label="Mecánico Responsable" 
                                placeholder="Nombre del mecánico"
                                value="{{ old('mecanico') }}"
                            />
                            @error('mecanico')<flux:error>{{ $message }}</flux:error>@enderror
                        </div>
                    </div>

                    <div>
                        <flux:textarea 
                            name="repuestos_usados" 
                            label="Repuestos Utilizados" 
                            placeholder="Lista de repuestos y materiales..."
                            rows="3"
                        >{{ old('repuestos_usados') }}</flux:textarea>
                        @error('repuestos_usados')<flux:error>{{ $message }}</flux:error>@enderror>
                    </div>
                </div>
            </div>

            {{-- Costo y Observaciones --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Costo y Observaciones</flux:heading>
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <flux:input 
                            type="number" 
                            name="costo" 
                            label="Costo Total (COP)" 
                            placeholder="0"
                            value="{{ old('costo', 0) }}"
                            min="0"
                            step="0.01"
                        />
                        @error('costo')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:textarea 
                            name="observaciones" 
                            label="Observaciones Adicionales" 
                            placeholder="Notas o comentarios adicionales..."
                            rows="3"
                        >{{ old('observaciones') }}</flux:textarea>
                        @error('observaciones')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3 bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:button :href="route('admin.maintenances.index')" variant="ghost">
                    Cancelar
                </flux:button>
                
                <flux:button type="submit" variant="primary" icon="check">
                    Registrar Mantenimiento
                </flux:button>
            </div>
        </form>

    </div>
</x-layouts.app>
