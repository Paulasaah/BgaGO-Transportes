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

        <form 
            method="POST" 
            action="{{ route('admin.reservations.store') }}" 
            class="space-y-6" 
            x-data="{
                tipo: '{{ old('tipo', 'reserva') }}',
                programarDomicilio: @json(old('programar_domicilio', false)),
                origen: {
                    query: '{{ old('direccion_origen') }}',
                    lat: '{{ old('lat_origen') }}',
                    lng: '{{ old('lon_origen') }}',
                    results: [],
                    loading: false,
                },
                destino: {
                    query: '{{ old('direccion_destino') }}',
                    lat: '{{ old('lat_destino') }}',
                    lng: '{{ old('lon_destino') }}',
                    results: [],
                    loading: false,
                },
                sedeAuto: null,
                sedeAutoDistancia: null,
                sedeAutoLoading: false,
                sedeAutoError: null,
                async searchLugar(target, query) {
                    if (!query || query.length < 3) {
                        this[target].results = [];
                        return;
                    }
                    this[target].loading = true;
                    try {
                        const url = `{{ route('admin.geocode.search') }}?q=${encodeURIComponent(query)}`;
                        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        if (!res.ok) return;
                        const data = await res.json();
                        if (data.success) {
                            this[target].results = data.results;
                        }
                    } catch (e) {
                        console.error('Error geocoding', e);
                    } finally {
                        this[target].loading = false;
                    }
                },
                async fetchNearestBranch(lat, lng) {
                    if (!lat || !lng) {
                        this.sedeAuto = null;
                        this.sedeAutoDistancia = null;
                        this.sedeAutoError = null;
                        return;
                    }
                    this.sedeAutoLoading = true;
                    this.sedeAutoError = null;
                    try {
                        const res = await fetch('{{ url('/api/branches/nearest') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ lat: lat, lon: lng })
                        });
                        if (!res.ok) {
                            this.sedeAuto = null;
                            this.sedeAutoDistancia = null;
                            this.sedeAutoError = 'No se pudo calcular la sede automáticamente.';
                            return;
                        }
                        const data = await res.json();
                        if (data.success && data.data && data.data.branch) {
                            this.sedeAuto = data.data.branch;
                            this.sedeAutoDistancia = data.data.distancia_km ?? null;
                        } else {
                            this.sedeAuto = null;
                            this.sedeAutoDistancia = null;
                            this.sedeAutoError = data.message || 'No se pudo calcular la sede automáticamente.';
                        }
                    } catch (e) {
                        console.error('Error buscando sede más cercana', e);
                        this.sedeAuto = null;
                        this.sedeAutoDistancia = null;
                        this.sedeAutoError = 'Error al calcular la sede más cercana.';
                    } finally {
                        this.sedeAutoLoading = false;
                    }
                },
                selectLugar(target, result) {
                    this[target].query = result.label;
                    this[target].lat = result.lat;
                    this[target].lng = result.lng;
                    this[target].results = [];

                    // Cuando se selecciona el origen en un domicilio, calcular sede automáticamente
                    if (target === 'origen' && this.tipo === 'domicilio') {
                        this.fetchNearestBranch(result.lat, result.lng);
                    }
                }
            }"
        >
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
                        {{-- Reserva clásica: siempre requiere inicio/fin --}}
                        <div x-show="tipo === 'reserva'">
                            <flux:input 
                                type="datetime-local" 
                                name="fecha_inicio" 
                                label="Fecha y Hora de Inicio"
                                value="{{ old('fecha_inicio', now()->format('Y-m-d\TH:i')) }}"
                            />
                        </div>

                        {{-- Domicilio: solo pedir fecha si se programa --}}
                        <div x-show="tipo === 'domicilio'" x-cloak>
                            <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300 mb-2">
                                <input 
                                    type="checkbox" 
                                    name="programar_domicilio" 
                                    value="1" 
                                    x-model="programarDomicilio"
                                    class="rounded border-zinc-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700"
                                >
                                <span>Programar recogida</span>
                            </label>

                            <div x-show="programarDomicilio" x-cloak>
                                <flux:input 
                                    type="datetime-local" 
                                    name="fecha_inicio" 
                                    label="Fecha y Hora de Recogida"
                                    value="{{ old('fecha_inicio', now()->format('Y-m-d\TH:i')) }}"
                                />
                            </div>
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
                                Para domicilios, la fecha de fin se calcula automáticamente según la duración estimada de la ruta
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Vehículo y Conductor --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">Asignación</flux:heading>
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div x-show="tipo === 'reserva'" x-cloak>
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
                        <div x-show="tipo === 'reserva'">
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 space-y-1">
                                <p class="text-xs text-zinc-700 dark:text-zinc-300">
                                    La sede se asignará automáticamente según la sede del vehículo seleccionado.
                                </p>
                            </div>
                        </div>

                        <div x-show="tipo === 'domicilio'" x-cloak>
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 space-y-1">
                                <p class="text-xs text-zinc-700 dark:text-zinc-300" x-show="!sedeAuto && !sedeAutoLoading && !sedeAutoError">
                                    La sede se asignará automáticamente cuando selecciones la dirección de origen.
                                </p>
                                <p class="text-xs text-zinc-700 dark:text-zinc-300" x-show="sedeAutoLoading">
                                    Calculando sede más cercana...
                                </p>
                                <p class="text-xs text-zinc-700 dark:text-zinc-300" x-show="sedeAuto">
                                    Sede asignada: <span x-text="sedeAuto.nombre"></span>
                                    <span x-show="sedeAutoDistancia !== null">
                                        (aprox. <span x-text="sedeAutoDistancia"></span> km)
                                    </span>
                                </p>
                                <p class="text-xs text-red-600 dark:text-red-400" x-show="sedeAutoError">
                                    <span x-text="sedeAutoError"></span>
                                </p>
                            </div>
                        </div>

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
                            x-model="origen.query"
                            @input.debounce.800ms="searchLugar('origen', origen.query)"
                        />
                        <input type="hidden" name="lat_origen" x-model="origen.lat">
                        <input type="hidden" name="lon_origen" x-model="origen.lng">
                        <template x-if="origen.results.length">
                            <div class="mt-1 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm max-h-48 overflow-auto text-sm">
                                <template x-for="result in origen.results" :key="result.label">
                                    <button
                                        type="button"
                                        class="w-full text-left px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                        @click="selectLugar('origen', result)"
                                        x-text="result.label"
                                    ></button>
                                </template>
                            </div>
                        </template>
                        @error('direccion_origen')<flux:error>{{ $message }}</flux:error>@enderror
                    </div>

                    <div>
                        <flux:input 
                            name="direccion_destino" 
                            label="Dirección de Destino" 
                            placeholder="Ej: Carrera 27 #54-32, Bucaramanga"
                            x-model="destino.query"
                            @input.debounce.800ms="searchLugar('destino', destino.query)"
                        />
                        <input type="hidden" name="lat_destino" x-model="destino.lat">
                        <input type="hidden" name="lon_destino" x-model="destino.lng">
                        <template x-if="destino.results.length">
                            <div class="mt-1 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm max-h-48 overflow-auto text-sm">
                                <template x-for="result in destino.results" :key="result.label">
                                    <button
                                        type="button"
                                        class="w-full text-left px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                        @click="selectLugar('destino', result)"
                                        x-text="result.label"
                                    ></button>
                                </template>
                            </div>
                        </template>
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
