<?php

use App\Models\Vehicle;
use App\Models\Branch;
use App\Services\ReservationService;
use App\Services\PricingService;
use App\Enums\DeliveryType;
use Illuminate\Support\Str;
use Livewire\Volt\Component;
use Carbon\Carbon;

new class extends Component {
    public array $vehiculos_disponibles = [];
    public ?int $vehiculo_seleccionado = null;
    public string $tipo_reserva = 'punto';
    public array $puntos_disponibles = [];
    public ?int $punto_recogida = null;
    public ?string $direccion_recogida = null;
    public ?float $lat_recogida = null;
    public ?float $lon_recogida = null;
    public ?string $fecha_inicio = null;
    public ?string $hora_inicio = null;
    public int $duracion_horas = 2;
    public int $precio_hora = 0;
    public int $total_estimado = 0;
    public int $recargo_domicilio = 2000;
    public ?string $fecha_fin_estimada = null;
    public ?string $notas_adicionales = null;

    public function mount(): void
    {
        $vehicles = Vehicle::query()
            ->disponibles()
            ->visiblesEnCatalogo()
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

        $this->vehiculos_disponibles = $vehicles->mapWithKeys(function ($v) {
            return [
                $v->id => [
                    'nombre' => trim(($v->marca . ' ' . $v->modelo)),
                    'precio' => (int) ($v->precio_hora ?? 0),
                ],
            ];
        })->toArray();

        $preselect = request()->integer('vehicle');
        if ($preselect && array_key_exists($preselect, $this->vehiculos_disponibles)) {
            $this->vehiculo_seleccionado = $preselect;
        } elseif (!empty($this->vehiculos_disponibles)) {
            $this->vehiculo_seleccionado = array_key_first($this->vehiculos_disponibles);
        }

        $this->precio_hora = $this->vehiculo_seleccionado ? ($this->vehiculos_disponibles[$this->vehiculo_seleccionado]['precio'] ?? 0) : 0;
        $this->total_estimado = $this->precio_hora * $this->duracion_horas;

        $branches = Branch::select('id', 'nombre')->orderBy('nombre')->get();
        $this->puntos_disponibles = $branches->mapWithKeys(fn($b) => [$b->id => $b->nombre])->toArray() ?: [1 => 'Sede Centro', 2 => 'Sede Cabecera'];
        $this->punto_recogida = array_key_first($this->puntos_disponibles);
    }

    public function updatedVehiculoSeleccionado(): void
    {
        $this->precio_hora = $this->vehiculo_seleccionado ? ($this->vehiculos_disponibles[$this->vehiculo_seleccionado]['precio'] ?? 0) : 0;
        $this->recalcular();
    }

    public function updatedFechaInicio(): void { $this->recalcular(); }
    public function updatedHoraInicio(): void { $this->recalcular(); }
    public function updatedDuracionHoras(): void { $this->recalcular(); }

    public function updatedLatRecogida(): void { $this->syncNearestBranch(); }
    public function updatedLonRecogida(): void { $this->syncNearestBranch(); }

    protected function recalcular(): void
    {
        if ($this->fecha_inicio && $this->hora_inicio) {
            $inicio = Carbon::parse($this->fecha_inicio . ' ' . $this->hora_inicio);
            $fin = (clone $inicio)->addHours($this->duracion_horas);
            $this->fecha_fin_estimada = $fin->format('d/m/Y H:i');
        } else {
            $this->fecha_fin_estimada = null;
        }

        $base = max(0, ($this->precio_hora ?? 0) * ($this->duracion_horas ?? 0));

        if ($this->tipo_reserva === 'domicilio' && $this->vehiculo_seleccionado && is_numeric($this->lat_recogida) && is_numeric($this->lon_recogida)) {
            $vehicle = Vehicle::find($this->vehiculo_seleccionado);
            if ($vehicle && $vehicle->branch && $vehicle->branch->lat && $vehicle->branch->lon) {
                $route = app(\App\Services\RouteService::class)->calculateRoute(
                    (float) $vehicle->branch->lat,
                    (float) $vehicle->branch->lon,
                    (float) $this->lat_recogida,
                    (float) $this->lon_recogida,
                );
                $distanciaKm = $route['success'] ? (float) ($route['data']['distance_km'] ?? 0.0) : $this->haversineKm(
                    (float) $vehicle->branch->lat,
                    (float) $vehicle->branch->lon,
                    (float) $this->lat_recogida,
                    (float) $this->lon_recogida
                );
                $pricing = app(PricingService::class)->calculateDeliveryPrice(DeliveryType::Vehiculo, max(0.1, $distanciaKm), $vehicle);
                $this->recargo_domicilio = (int) ($pricing['data']['total'] ?? self::fallbackRecargo());
            } else {
                $this->recargo_domicilio = self::fallbackRecargo();
            }
        } else {
            $this->recargo_domicilio = self::fallbackRecargo();
        }

        $this->total_estimado = $base + ($this->tipo_reserva === 'domicilio' ? $this->recargo_domicilio : 0);
    }

    private static function fallbackRecargo(): int
    {
        return 2000;
    }

    private function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earth = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earth * $c, 2);
    }

    private function syncNearestBranch(): void
    {
        if (!is_numeric($this->lat_recogida) || !is_numeric($this->lon_recogida)) {
            return;
        }

        $nearest = Branch::findNearestTo((float) $this->lat_recogida, (float) $this->lon_recogida);
        if (!$nearest) {
            return;
        }

        $this->punto_recogida = $nearest->id;

        $vehicles = Vehicle::query()
            ->disponibles()
            ->visiblesEnCatalogo()
            ->porSede($nearest->id)
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

        $this->vehiculos_disponibles = $vehicles->mapWithKeys(function ($v) {
            return [
                $v->id => [
                    'nombre' => trim(($v->marca . ' ' . $v->modelo)),
                    'precio' => (int) ($v->precio_hora ?? 0),
                ],
            ];
        })->toArray();

        if (empty($this->vehiculos_disponibles)) {
            // Mantener estado previo si no hay vehículos en la sede
            return;
        }

        if (!array_key_exists($this->vehiculo_seleccionado, $this->vehiculos_disponibles)) {
            $this->vehiculo_seleccionado = array_key_first($this->vehiculos_disponibles);
        }

        $this->precio_hora = $this->vehiculo_seleccionado ? ($this->vehiculos_disponibles[$this->vehiculo_seleccionado]['precio'] ?? 0) : 0;
        $this->recalcular();
    }

    public function continuar()
    {
        $this->validate([
            'vehiculo_seleccionado' => ['required', 'integer'],
            'tipo_reserva' => ['required', 'in:punto,domicilio'],
            'fecha_inicio' => ['required', 'date'],
            'hora_inicio' => ['required'],
            'duracion_horas' => ['required', 'integer', 'min:1', 'max:24'],
        ]);

        if ($this->tipo_reserva === 'punto') {
            $this->validate(['punto_recogida' => ['required', 'integer']]);
        } else {
            $this->validate(['direccion_recogida' => ['required', 'string', 'min:6']]);
        }

        $inicio = Carbon::parse($this->fecha_inicio . ' ' . $this->hora_inicio);
        $fin = (clone $inicio)->addHours($this->duracion_horas);

        $vehicle = Vehicle::findOrFail($this->vehiculo_seleccionado);
        $origenNombre = $vehicle->branch?->nombre ?? (Branch::find($this->punto_recogida)?->nombre ?? 'Sede');
        $destinoDireccion = $this->tipo_reserva === 'domicilio'
            ? ($this->direccion_recogida ?? 'Domicilio')
            : $origenNombre;

        $service = app(ReservationService::class);
        $payload = [
            'user_id' => auth()->id(),
            'vehiculo_id' => $vehicle->id,
            'sede_id' => $vehicle->sede_id,
            'fecha_inicio' => $inicio,
            'fecha_fin' => $fin,
            'origen_direccion' => $origenNombre,
            'destino_direccion' => $destinoDireccion,
            'notas_cliente' => $this->notas_adicionales,
            'entrega_domicilio' => ($this->tipo_reserva === 'domicilio'),
        ];

        if ($this->tipo_reserva === 'domicilio' && is_numeric($this->lat_recogida) && is_numeric($this->lon_recogida)) {
            $payload['destino_lat'] = (float) $this->lat_recogida;
            $payload['destino_lng'] = (float) $this->lon_recogida;
        }

        $result = $service->createReservation($payload);

        if (!$result['success']) {
            session()->flash('error', $result['message'] ?? 'No se pudo crear la reserva');
            return;
        }

        $reserva = $result['data'];

        session()->put('reserva_temporal', [
            'id' => $reserva->id,
            'codigo' => $reserva->codigo,
            'vehiculo' => $reserva->vehicle?->getFullName() ?? ($this->vehiculos_disponibles[$this->vehiculo_seleccionado]['nombre'] ?? 'Vehículo'),
            'tipo_reserva' => $this->tipo_reserva,
            'fecha_inicio' => $this->fecha_inicio,
            'hora_inicio' => $this->hora_inicio,
            'duracion_horas' => $this->duracion_horas,
            'total' => $reserva->monto_final ?? $this->total_estimado,
        ]);

        session()->put('reserva_db', [
            'id' => $reserva->id,
            'codigo' => $reserva->codigo,
            'monto_final' => $reserva->monto_final,
        ]);

        $this->redirect(route('catalog.payment', absolute: false), navigate: true);
        $this->js('window.location.href = "' . route('catalog.payment') . '"');
    }
}; ?>

<div class="p-8">
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif
    <form wire:submit.prevent="continuar" class="space-y-8">

        <!-- Selección de Vehículo -->
        <div>
            <label class="block text-lg font-semibold text-zinc-900 dark:text-white mb-4">
                Selecciona tu Vehículo *
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($vehiculos_disponibles as $key => $vehiculo)
                <label class="relative cursor-pointer group">
                    <input
                        type="radio"
                        wire:model.live="vehiculo_seleccionado"
                        name="vehiculo_seleccionado"
                        value="{{ $key }}"
                        class="peer sr-only"
                    >
                    <div class="p-4 rounded-xl border-2 border-zinc-300 dark:border-zinc-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/30 transition-all hover:border-blue-400 hover:shadow-md h-28 flex flex-col justify-between">
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-1 overflow-hidden text-ellipsis whitespace-nowrap">{{ $vehiculo['nombre'] }}</h4>
                        <div>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ number_format($vehiculo['precio'], 0, ',', '.') }}</p>
                            <p class="text-xs text-zinc-500">por hora</p>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
            @error('vehiculo_seleccionado')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-8"></div>

        <!-- Tipo de Reserva -->
        <div>
            <label class="block text-lg font-semibold text-zinc-900 dark:text-white mb-4">
                Tipo de Reserva *
            </label>
            <div class="grid grid-cols-2 gap-4">
                <label class="relative cursor-pointer">
                    <input
                        type="radio"
                        wire:model.live="tipo_reserva"
                        name="tipo_reserva"
                        value="punto"
                        class="peer sr-only"
                    >
                    <div class="p-6 rounded-xl border-2 border-zinc-300 dark:border-zinc-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/30 transition-all hover:border-blue-400 text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-zinc-600 dark:text-zinc-400 peer-checked:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-2">Recoger en Punto</h4>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Recoge el vehículo en una de nuestras sedes</p>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input
                        type="radio"
                        wire:model.live="tipo_reserva"
                        name="tipo_reserva"
                        value="domicilio"
                        class="peer sr-only"
                    >
                    <div class="p-6 rounded-xl border-2 border-zinc-300 dark:border-zinc-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/30 transition-all hover:border-blue-400 text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-zinc-600 dark:text-zinc-400 peer-checked:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-2">Entrega a Domicilio</h4>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Te llevamos el vehículo donde estés (+$2.000 + $1.500/km)</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Punto de Recogida o Dirección -->
        @if($tipo_reserva === 'punto')
        <div>
            <label for="punto_recogida" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                Punto de Recogida *
            </label>
            <select
                id="punto_recogida"
                wire:model="punto_recogida"
                class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            >
                <option value="">Selecciona un punto</option>
                @foreach($puntos_disponibles as $key => $punto)
                <option value="{{ $key }}">{{ $punto }}</option>
                @endforeach
            </select>
            @error('punto_recogida')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        @else
        <div x-data="{
            results: [],
            loading: false,
            async search(val) {
                if (!val || val.length < 3) { this.results = []; return; }
                this.loading = true;
                try {
                    const url = `{{ route('user.geocode.search') }}?q=${encodeURIComponent(val)}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) { this.results = []; return; }
                    const data = await res.json();
                    this.results = data.success ? data.results : [];
                } catch (e) {
                    this.results = [];
                } finally {
                    this.loading = false;
                }
            },
            select(r) {
                $wire.set('direccion_recogida', r.label);
                $wire.set('lat_recogida', r.lat);
                $wire.set('lon_recogida', r.lng);
                this.results = [];
            }
        }">
            <label for="direccion_recogida" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                Dirección de Entrega *
            </label>
            <div class="relative">
                <input
                    type="text"
                    id="direccion_recogida"
                    wire:model="direccion_recogida"
                    @input.debounce.500ms="search($event.target.value)"
                    placeholder="Ej: Calle 36 #10-20, Bucaramanga"
                    class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                >
                <template x-if="results.length || loading">
                    <div class="absolute left-0 right-0 mt-1 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm max-h-48 overflow-auto text-sm z-20">
                        <div x-show="loading" class="px-3 py-2 text-zinc-500 dark:text-zinc-400">Buscando…</div>
                        <template x-for="result in results" :key="result.label">
                            <button
                                type="button"
                                class="w-full text-left px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                @click="select(result)"
                                x-text="result.label"
                            ></button>
                        </template>
                    </div>
                </template>
            </div>
            @error('direccion_recogida')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        @endif

        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-8"></div>

        <!-- Fecha y Hora de Inicio -->
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label for="fecha_inicio" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                    Fecha de Inicio *
                </label>
                <input
                    type="date"
                    id="fecha_inicio"
                    wire:model.live="fecha_inicio"
                    min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                    class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                >
                @error('fecha_inicio')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="hora_inicio" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                    Hora de Inicio *
                </label>
                <input
                    type="time"
                    id="hora_inicio"
                    wire:model.live="hora_inicio"
                    class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                >
                @error('hora_inicio')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Duración -->
        <div>
            <label for="duracion_horas" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                Duración (horas) *
            </label>
            <div class="flex items-center gap-4">
                <input
                    type="range"
                    id="duracion_horas"
                    wire:model.live="duracion_horas"
                    min="1"
                    max="24"
                    class="flex-1 h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer dark:bg-zinc-700"
                >
                <div class="w-20 px-4 py-2 bg-blue-100 dark:bg-blue-950/50 rounded-lg text-center">
                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $duracion_horas }}</span>
                    <span class="text-xs text-zinc-600 dark:text-zinc-400 block">hora{{ $duracion_horas > 1 ? 's' : '' }}</span>
                </div>
            </div>
            @error('duracion_horas')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Notas Adicionales -->
        <div>
            <label for="notas_adicionales" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                Notas Adicionales (Opcional)
            </label>
            <textarea
                id="notas_adicionales"
                wire:model="notas_adicionales"
                rows="3"
                placeholder="¿Alguna instrucción especial o preferencia?"
                class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
            ></textarea>
        </div>

        <!-- Resumen de la Reserva -->
        @if($vehiculo_seleccionado && $fecha_inicio && $hora_inicio)
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-950/30 dark:to-blue-900/30 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
            <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">Resumen de la Reserva</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">Vehículo:</span>
                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $vehiculos_disponibles[$vehiculo_seleccionado]['nombre'] ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">Duración:</span>
                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $duracion_horas }} hora{{ $duracion_horas > 1 ? 's' : '' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">Precio por hora:</span>
                    <span class="font-semibold text-zinc-900 dark:text-white">${{ number_format($precio_hora, 0, ',', '.') }}</span>
                </div>
                @if($tipo_reserva === 'domicilio')
                <div class="flex justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">Entrega a domicilio:</span>
                    <span class="font-semibold text-zinc-900 dark:text-white">${{ number_format($recargo_domicilio, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($fecha_fin_estimada)
                <div class="flex justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">Devolución estimada:</span>
                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $fecha_fin_estimada }}</span>
                </div>
                @endif
                <div class="border-t border-blue-200 dark:border-blue-700 pt-3 mt-3"></div>
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-zinc-900 dark:text-white">Total:</span>
                    <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                        ${{ number_format($total_estimado, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
        @endif

        <!-- Botones -->
        <div class="flex gap-4">
            <a href="{{ route('catalog.index') }}"
               class="px-6 py-4 bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-900 dark:text-white rounded-lg font-semibold transition-colors text-center">
                Cancelar
            </a>
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="flex-1 px-6 py-4 bg-blue-600 hover:bg-blue-700 disabled:bg-zinc-400 text-white rounded-lg font-semibold text-lg transition-all hover:scale-[1.02] disabled:hover:scale-100 shadow-lg hover:shadow-xl disabled:cursor-not-allowed"
            >
                <span wire:loading.remove wire:target="continuar">
                    Continuar al Pago
                </span>
                <span wire:loading wire:target="continuar" class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Procesando...
                </span>
            </button>
        </div>

    </form>
</div>
