<?php

use Livewire\Volt\Component;
use App\Models\Branch;
use App\Services\DeliveryService;

new class extends Component {
    public ?string $direccion_origen = null;
    public ?string $direccion_destino = null;
    public ?string $tamano_paquete = 'pequeno';
    public ?string $descripcion_paquete = null;
    public ?string $nombre_destinatario = null;
    public ?string $telefono_destinatario = null;
    public bool $requiere_seguro = false;
    public float $distancia_estimada = 0.0;
    public int $tarifa_estimada = 0;
    public ?int $sede_id = null;
    public float $lat_origen = 0.0;
    public float $lon_origen = 0.0;
    public float $lat_destino = 0.0;
    public float $lon_destino = 0.0;

    public function mount(): void
    {
        $branch = Branch::select('id', 'lat', 'lon', 'nombre')->first();
        $this->sede_id = $branch?->id ?? 1;
        $baseLat = (float) ($branch?->lat ?? 7.119);
        $baseLon = (float) ($branch?->lon ?? -73.122);
        $this->lat_origen = $baseLat;
        $this->lon_origen = $baseLon;
        $this->lat_destino = $baseLat + 0.01;
        $this->lon_destino = $baseLon + 0.01;
        $this->recalcular();
    }

    public function updatedDireccionOrigen(): void { $this->recalcular(); }
    public function updatedDireccionDestino(): void { $this->recalcular(); }
    public function updatedTamanoPaquete(): void { $this->recalcular(); }
    public function updatedRequiereSeguro(): void { $this->recalcular(); }

    protected function recalcular(): void
    {
        $len = strlen(($this->direccion_origen ?? '') . ($this->direccion_destino ?? ''));
        $km = max(1, min(15, (int) floor($len / 10)));
        $this->distancia_estimada = (float) $km;

        $base = 6000 + ($km * 1000);
        $mult = $this->tamano_paquete === 'grande' ? 1.6 : ($this->tamano_paquete === 'mediano' ? 1.3 : 1.0);
        $this->tarifa_estimada = (int) round($base * $mult);

        $this->lat_destino = $this->lat_origen + ($km * 0.005);
        $this->lon_destino = $this->lon_origen + ($km * 0.005);
    }

    public function solicitar(): void
    {
        $this->validate([
            'direccion_origen' => ['required', 'string', 'min:6'],
            'direccion_destino' => ['required', 'string', 'min:6'],
            'tamano_paquete' => ['required', 'in:pequeno,mediano,grande'],
            'descripcion_paquete' => ['required', 'string', 'min:3'],
            'nombre_destinatario' => ['nullable', 'string'],
            'telefono_destinatario' => ['nullable', 'string'],
        ]);

        $service = app(DeliveryService::class);
        $result = $service->createPackageDelivery([
            'user_id' => auth()->id(),
            'sede_id' => $this->sede_id,
            'direccion_origen' => $this->direccion_origen,
            'direccion_destino' => $this->direccion_destino,
            'lat_origen' => $this->lat_origen,
            'lon_origen' => $this->lon_origen,
            'lat_destino' => $this->lat_destino,
            'lon_destino' => $this->lon_destino,
            'nombre_remitente' => auth()->user()?->name ?? 'Cliente',
            'telefono_remitente' => auth()->user()?->phone ?? '0000000000',
            'nombre_destinatario' => $this->nombre_destinatario ?? 'Destinatario',
            'telefono_destinatario' => $this->telefono_destinatario ?? '0000000000',
            'descripcion_contenido' => $this->descripcion_paquete,
            'peso_kg' => $this->tamano_paquete === 'grande' ? 7 : ($this->tamano_paquete === 'mediano' ? 3 : 1),
            'requiere_firma' => true,
            'es_fragil' => false,
            'instrucciones_especiales' => null,
            'costo' => $this->tarifa_estimada + ($this->requiere_seguro ? 2000 : 0),
            'fecha_recogida' => now()->addHour(),
        ]);

        if (!$result['success']) {
            session()->flash('error', $result['message'] ?? 'No se pudo crear el domicilio');
            return;
        }

        $delivery = $result['data'];
        $reserva = $delivery->reservation;

        session()->put('reserva_temporal', [
            'id' => $reserva->id,
            'codigo' => $reserva->codigo,
            'vehiculo' => 'domicilio',
            'tipo_reserva' => 'domicilio',
            'fecha_inicio' => $reserva->fecha_inicio?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'hora_inicio' => $reserva->fecha_inicio?->format('H:i') ?? now()->format('H:i'),
            'duracion_horas' => max(1, (int) ceil(($reserva->duracion_minutos ?? 60) / 60)),
            'total' => (int) ($reserva->monto_final ?? $this->tarifa_estimada),
        ]);

        session()->put('reserva_db', [
            'id' => $reserva->id,
            'codigo' => $reserva->codigo,
            'monto_final' => (int) ($reserva->monto_final ?? $this->tarifa_estimada),
        ]);

        $this->redirect(route('catalog.payment', absolute: false), navigate: true);
    }
}; ?>

<div class="space-y-6">
    <form wire:submit="solicitar" class="space-y-6 p-3 md:p-4">

        <!-- Dirección de Origen -->
        <div>
            <label for="direccion_origen" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Dirección de Origen *
                </span>
            </label>
            <input
                type="text"
                id="direccion_origen"
                wire:model.live.debounce.500ms="direccion_origen"
                placeholder="Ej: Calle 36 #10-20, Bucaramanga"
                class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            >
            @error('direccion_origen')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Dirección de Destino -->
        <div>
            <label for="direccion_destino" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    Dirección de Destino *
                </span>
            </label>
            <input
                type="text"
                id="direccion_destino"
                wire:model.live.debounce.500ms="direccion_destino"
                placeholder="Ej: Carrera 27 #42-15, Floridablanca"
                class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            >
            @error('direccion_destino')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tamaño del Paquete -->
        <div>
            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-3">
                Tamaño del Paquete *
            </label>
            <div class="grid grid-cols-3 gap-4">
                <label class="relative cursor-pointer">
                    <input
                        type="radio"
                        name="tamano_paquete"
                        wire:model.live="tamano_paquete"
                        value="pequeno"
                        class="peer sr-only"
                    >
                    <div class="p-4 rounded-lg border-2 border-zinc-300 dark:border-zinc-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/30 transition-all text-center hover:border-blue-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-zinc-600 dark:text-zinc-400 peer-checked:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span class="text-sm font-medium text-zinc-900 dark:text-white">Pequeño</span>
                        <p class="text-xs text-zinc-500 mt-1">Hasta 2kg</p>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input
                        type="radio"
                        name="tamano_paquete"
                        wire:model.live="tamano_paquete"
                        value="mediano"
                        class="peer sr-only"
                    >
                    <div class="p-4 rounded-lg border-2 border-zinc-300 dark:border-zinc-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/30 transition-all text-center hover:border-blue-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-zinc-600 dark:text-zinc-400 peer-checked:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span class="text-sm font-medium text-zinc-900 dark:text-white">Mediano</span>
                        <p class="text-xs text-zinc-500 mt-1">2kg - 5kg</p>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input
                        type="radio"
                        name="tamano_paquete"
                        wire:model.live="tamano_paquete"
                        value="grande"
                        class="peer sr-only"
                    >
                    <div class="p-4 rounded-lg border-2 border-zinc-300 dark:border-zinc-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/30 transition-all text-center hover:border-blue-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-zinc-600 dark:text-zinc-400 peer-checked:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span class="text-sm font-medium text-zinc-900 dark:text-white">Grande</span>
                        <p class="text-xs text-zinc-500 mt-1">5kg - 10kg</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Descripción del Paquete -->
        <div>
            <label for="descripcion_paquete" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                Descripción del Paquete *
            </label>
            <textarea
                id="descripcion_paquete"
                wire:model="descripcion_paquete"
                rows="3"
                placeholder="Describe brevemente el contenido del paquete"
                class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
            ></textarea>
            @error('descripcion_paquete')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Grid para Nombre y Teléfono del Destinatario -->
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label for="nombre_destinatario" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                    Nombre del Destinatario
                </label>
                <input
                    type="text"
                    id="nombre_destinatario"
                    wire:model="nombre_destinatario"
                    placeholder="Nombre completo"
                    class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                >
            </div>

            <div>
                <label for="telefono_destinatario" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                    Teléfono del Destinatario
                </label>
                <input
                    type="tel"
                    id="telefono_destinatario"
                    wire:model="telefono_destinatario"
                    placeholder="300 123 4567"
                    class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                >
            </div>
        </div>

        <!-- Seguro -->
        <div class="flex items-center">
            <label class="relative inline-flex items-center cursor-pointer">
                <input
                    type="checkbox"
                    wire:model="requiere_seguro"
                    class="sr-only peer"
                >
                <div class="w-11 h-6 bg-zinc-300 dark:bg-zinc-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                <span class="ms-3 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Incluir seguro adicional (+$2.000)
                </span>
            </label>
        </div>

        <!-- Resumen de Tarifa -->
        @if(strlen($direccion_origen ?? '') > 0 && strlen($direccion_destino ?? '') > 0)
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-950/30 dark:to-blue-900/30 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Distancia estimada</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($distancia_estimada, 1) }} km</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Tarifa estimada</p>
                        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                            ${{ number_format($tarifa_estimada + ($requiere_seguro ? 2000 : 0), 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    La tarifa final puede variar según la ruta real del conductor.
                </p>
            </div>
        @endif

        <!-- Botón de Envío -->
        <div class="flex gap-4">
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="flex-1 px-6 py-4 bg-blue-600 hover:bg-blue-700 disabled:bg-zinc-400 text-white rounded-lg font-semibold text-lg transition-all hover:scale-[1.02] disabled:hover:scale-100 shadow-lg hover:shadow-xl disabled:cursor-not-allowed"
            >
                <span wire:loading.remove wire:target="solicitar">
                    Solicitar Domicilio
                </span>
                <span wire:loading wire:target="solicitar" class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Procesando...
                </span>
            </button>
        </div>

    </form>

    <!-- Mensaje de éxito -->
    @if (session()->has('success'))
        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-blue-800 dark:text-blue-200">{{ session('success') }}</p>
            </div>
        </div>
    @endif
</div>
