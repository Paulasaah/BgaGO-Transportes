@php
use App\Enums\ReservationType;
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Crear Reserva</flux:heading>
                <flux:subheading>Registra una nueva reserva en el sistema</flux:subheading>
            </div>
            
            <flux:button :href="route('admin.reservations.index')" variant="ghost" icon="arrow-left">
                Volver
            </flux:button>
        </div>

        <form method="POST" action="{{ route('admin.reservations.store') }}" class="space-y-6" x-data="{ tipo: '{{ old('tipo', 'reserva') }}' }">
            @csrf

            {{-- Información del Cliente --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Cliente y Tipo de Servicio</flux:heading>
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <flux:select name="user_id" label="Usuario" placeholder="Selecciona un usuario" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('user_id')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:select name="tipo" label="Tipo de Servicio" placeholder="Selecciona tipo" required x-model="tipo">
                            @foreach(ReservationType::cases() as $tipoEnum)
                                <option value="{{ $tipoEnum->value }}" {{ old('tipo', 'reserva') === $tipoEnum->value ? 'selected' : '' }}>
                                    {{ $tipoEnum->label() }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('tipo')<flux:error>{{ $message }}</flux:error>@enderror
                        
                        <div class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                            <span x-show="tipo === 'reserva'">📅 Alquiler de vehículo por tiempo</span>
                            <span x-show="tipo === 'domicilio'">Servicio de entrega punto a punto</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fechas y Horarios --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Fechas y Horarios</flux:heading>
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <div x-show="tipo === 'reserva'">
                            <flux:input 
                                type="datetime-local" 
                                name="fecha_inicio" 
                                label="Fecha y Hora de Inicio"
                                value="{{ old('fecha_inicio', now()->format('Y-m-d\TH:i')) }}"
                                required
                            />
                        </div>
                        <div x-show="tipo === 'domicilio'">
                            <flux:input 
                                type="datetime-local" 
                                name="fecha_inicio" 
                                label="Fecha y Hora de Recogida"
                                value="{{ old('fecha_inicio', now()->format('Y-m-d\TH:i')) }}"
                                required
                            />
                        </div>
                        @error('fecha_inicio')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div x-show="tipo === 'reserva'">
                        <flux:input 
                            type="datetime-local" 
                            name="fecha_fin" 
                            label="Fecha y Hora de Fin" 
                            value="{{ old('fecha_fin', now()->addHours(2)->format('Y-m-d\TH:i')) }}"
                        />
                        @error('fecha_fin')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>
                    
                    <div x-show="tipo === 'domicilio'" class="md:col-span-2">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                            <p class="text-sm text-blue-700 dark:text-blue-400">
                                ℹ️ Para domicilios, la fecha de fin se calcula automáticamente según la duración del servicio
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Vehículo y Conductor --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Asignación</flux:heading>
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <flux:select name="vehicle_id" label="Vehículo" placeholder="Selecciona un vehículo">
                            <option value="">Sin asignar</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->placa }} - {{ $vehicle->marca }} {{ $vehicle->modelo }} ({{ $vehicle->tipo->label() }})
                                </option>
                            @endforeach
                        </flux:select>
                        @error('vehicle_id')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:select name="conductor_id" label="Conductor" placeholder="Selecciona un conductor">
                            <option value="">Sin asignar</option>
                            @foreach($conductores as $conductor)
                                <option value="{{ $conductor->id }}" {{ old('conductor_id') == $conductor->id ? 'selected' : '' }}>
                                    {{ $conductor->name }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('conductor_id')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:select name="sede_id" label="Sede" placeholder="Selecciona una sede">
                            <option value="">Sin asignar</option>
                            @foreach($sedes as $sede)
                                <option value="{{ $sede->id }}" {{ old('sede_id') == $sede->id ? 'selected' : '' }}>
                                    {{ $sede->nombre }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('sede_id')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>
                </div>
            </div>

            {{-- Ubicaciones (solo para domicilios) --}}
            <div x-show="tipo === 'domicilio'" class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Ubicaciones</flux:heading>
                <flux:subheading class="mb-4">Direcciones de origen y destino del domicilio</flux:subheading>
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <flux:input 
                            name="direccion_origen" 
                            label="Dirección de Origen" 
                            placeholder="Ej: Calle 45 #23-12, Bucaramanga"
                            value="{{ old('direccion_origen') }}"
                        />
                        @error('direccion_origen')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:input 
                            name="direccion_destino" 
                            label="Dirección de Destino" 
                            placeholder="Ej: Carrera 27 #54-32, Bucaramanga"
                            value="{{ old('direccion_destino') }}"
                        />
                        @error('direccion_destino')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>
                </div>
            </div>

            {{-- Observaciones --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Observaciones</flux:heading>
                
                <div>
                    <flux:textarea 
                        name="observaciones" 
                        label="Notas adicionales" 
                        placeholder="Información adicional sobre la reserva..."
                        rows="3"
                    >{{ old('observaciones') }}</flux:textarea>
                    @error('observaciones')<flux:error>{{ $message }}</flux:error>@enderror
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3 bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:button :href="route('admin.reservations.index')" variant="ghost">
                    Cancelar
                </flux:button>
                
                <flux:button type="submit" variant="primary" icon="check">
                    Crear Reserva
                </flux:button>
            </div>
        </form>

    </div>
</x-layouts.app>