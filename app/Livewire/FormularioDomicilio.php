<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Branch;
use App\Services\DeliveryService;
use App\Services\PricingService;

class FormularioDomicilio extends Component
{
    public ?string $direccion_origen = null;
    public ?string $direccion_destino = null;
    public string $tamano_paquete = 'pequeno';
    public ?string $descripcion_paquete = null;
    public ?string $nombre_destinatario = null;
    public ?string $telefono_destinatario = null;
    public bool $requiere_seguro = false;

    public float $distancia_estimada = 0.0;
    public int $tarifa_estimada = 0;

    public ?float $lat_origen = null;
    public ?float $lon_origen = null;
    public ?float $lat_destino = null;
    public ?float $lon_destino = null;

    public array $puntos_disponibles = [];
    public ?int $sede_id = null;

    protected DeliveryService $deliveryService;
    protected PricingService $pricingService;

    protected $listeners = [
        'setPickup' => 'setPickup',
        'setDropoff' => 'setDropoff',
    ];

    public function boot(DeliveryService $deliveryService, PricingService $pricingService): void
    {
        $this->deliveryService = $deliveryService;
        $this->pricingService = $pricingService;
    }

    public function mount(): void
    {
        $this->puntos_disponibles = Branch::all()->mapWithKeys(fn($b) => [$b->id => $b->nombre])->toArray();
        $this->sede_id = array_key_first($this->puntos_disponibles);

        $this->direccion_origen = request()->string('pickup')->toString() ?: $this->direccion_origen;
        $this->direccion_destino = request()->string('dropoff')->toString() ?: $this->direccion_destino;

        $this->lat_origen = request()->has('pickup_lat') ? (float) request()->get('pickup_lat') : $this->lat_origen;
        $this->lon_origen = request()->has('pickup_lng') ? (float) request()->get('pickup_lng') : $this->lon_origen;
        $this->lat_destino = request()->has('drop_lat') ? (float) request()->get('drop_lat') : $this->lat_destino;
        $this->lon_destino = request()->has('drop_lng') ? (float) request()->get('drop_lng') : $this->lon_destino;

        $this->recalcular();
    }

    public function updatedDireccionOrigen(): void { $this->recalcular(); }
    public function updatedDireccionDestino(): void { $this->recalcular(); }
    public function updatedTamanoPaquete(): void { $this->recalcular(); }
    public function updatedRequiereSeguro(): void { $this->recalcular(); }

    public function setPickup($payload): void
    {
        $this->direccion_origen = $payload['direccion'] ?? $this->direccion_origen;
        $this->lat_origen = isset($payload['lat']) ? (float) $payload['lat'] : $this->lat_origen;
        $this->lon_origen = isset($payload['lng']) ? (float) $payload['lng'] : $this->lon_origen;
        $this->recalcular();
    }

    public function setDropoff($payload): void
    {
        $this->direccion_destino = $payload['direccion'] ?? $this->direccion_destino;
        $this->lat_destino = isset($payload['lat']) ? (float) $payload['lat'] : $this->lat_destino;
        $this->lon_destino = isset($payload['lng']) ? (float) $payload['lng'] : $this->lon_destino;
        $this->recalcular();
    }

    private function recalcular(): void
    {
        $kmBase = match ($this->tamano_paquete) {
            'pequeno' => 3.0,
            'mediano' => 5.0,
            'grande' => 8.0,
            default => 4.0,
        };

        $hasCoords = is_numeric($this->lat_origen) && is_numeric($this->lon_origen)
            && is_numeric($this->lat_destino) && is_numeric($this->lon_destino);

        $this->distancia_estimada = $hasCoords ? max(0.1, $this->calculateDistanceLocal()) : $kmBase;

        $pricing = $this->pricingService->calculateDeliveryPrice(
            \App\Enums\DeliveryType::Paquete,
            $this->distancia_estimada
        );

        $base = (int) ($pricing['data']['total'] ?? 0);
        $this->tarifa_estimada = $base + ($this->requiere_seguro ? 2000 : 0);
    }

    private function calculateDistanceLocal(): float
    {
        $earthRadius = 6371;
        $lat1 = deg2rad((float) $this->lat_origen);
        $lon1 = deg2rad((float) $this->lon_origen);
        $lat2 = deg2rad((float) $this->lat_destino);
        $lon2 = deg2rad((float) $this->lon_destino);
        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;
        $a = sin($latDelta/2)**2 + cos($lat1) * cos($lat2) * sin($lonDelta/2)**2;
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return round($earthRadius * $c, 2);
    }

    public function solicitar()
    {
        $this->validate($this->rules(), $this->messages());

        $result = $this->deliveryService->createPackageDelivery([
            'user_id' => auth()->id(),
            'sede_id' => $this->sede_id,
            'direccion_origen' => $this->direccion_origen,
            'lat_origen' => $this->lat_origen ?? 0,
            'lon_origen' => $this->lon_origen ?? 0,
            'direccion_destino' => $this->direccion_destino,
            'lat_destino' => $this->lat_destino ?? 0,
            'lon_destino' => $this->lon_destino ?? 0,
            'nombre_remitente' => auth()->user()->name ?? 'Cliente',
            'telefono_remitente' => auth()->user()->phone ?? '0000000000',
            'nombre_destinatario' => $this->nombre_destinatario,
            'telefono_destinatario' => $this->telefono_destinatario,
            'descripcion_contenido' => $this->descripcion_paquete,
            'instrucciones_especiales' => null,
        ]);

        if (!$result['success']) {
            $this->dispatch('notify', type: 'error', message: $result['message']);
            return;
        }

        $delivery = $result['data'];
        $reservation = $delivery->reservation;
        $duracionMin = (int) ($reservation->duracion_minutos ?? 60);
        $duracionHoras = max(1, (int) ceil($duracionMin / 60));

        session([
            'reserva_temporal' => [
                'vehiculo' => 'domicilio-paquete',
                'tipo_reserva' => 'domicilio',
                'fecha_inicio' => optional($reservation->fecha_inicio)->format('Y-m-d'),
                'hora_inicio' => optional($reservation->fecha_inicio)->format('H:i'),
                'duracion_horas' => $duracionHoras,
                'total' => (int) ($reservation->monto_final ?? $delivery->costo ?? 0),
            ],
        ]);
        $this->redirectRoute('catalog.payment');
    }

    public function rules(): array
    {
        return [
            'direccion_origen' => 'required|string|min:6',
            'direccion_destino' => 'required|string|min:6',
            'tamano_paquete' => 'required|in:pequeno,mediano,grande',
            'nombre_destinatario' => 'nullable|string|min:3',
            'telefono_destinatario' => 'nullable|string|min:7',
            'sede_id' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'direccion_origen.required' => 'Ingresa la dirección de recogida',
            'direccion_destino.required' => 'Ingresa la dirección de entrega',
            'sede_id.required' => 'Selecciona la sede base',
        ];
    }

    public function render()
    {
        return view('livewire.formulario-domicilio');
    }
}
