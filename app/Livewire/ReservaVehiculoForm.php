<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Vehicle;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReservaVehiculoForm extends Component
{
    public array $vehiculos_disponibles = [];

    public ?int $vehiculo_seleccionado = null;

    public string $tipo_reserva = 'punto';

    public array $puntos_disponibles = [];

    public ?int $punto_recogida = null;

    public ?string $direccion_recogida = null;

    public ?string $fecha_inicio = null;

    public ?string $hora_inicio = null;

    public int $duracion_horas = 1;

    public ?string $notas_adicionales = null;

    public int $precio_hora = 0;

    public ?string $fecha_fin_estimada = null;

    public int $total_estimado = 0;

    protected ReservationService $reservationService;

    public function boot(ReservationService $reservationService): void
    {
        $this->reservationService = $reservationService;
    }

    public function mount(): void
    {
        $vehicles = Vehicle::disponibles()->visiblesEnCatalogo()->orderBy('id', 'desc')->get(['id', 'marca', 'modelo', 'tipo', 'precio_hora', 'sede_id']);
        $this->vehiculos_disponibles = $vehicles->map(function ($v) {
            return [
                'id' => $v->id,
                'nombre' => $v->getFullName(),
                'precio' => (int) $v->precio_hora,
                'sede_id' => $v->sede_id,
            ];
        })->values()->toArray();

        $this->puntos_disponibles = Branch::all()->mapWithKeys(function ($b) {
            return [$b->id => $b->nombre.($b->direccion ? ' - '.$b->direccion : '')];
        })->toArray();

        $vehicleId = request()->integer('vehicle');
        if ($vehicleId) {
            foreach ($this->vehiculos_disponibles as $idx => $item) {
                if ($item['id'] === $vehicleId) {
                    $this->vehiculo_seleccionado = $idx;
                    break;
                }
            }
            $this->updatedVehiculoSeleccionado();
        }

        $this->recalcular();
    }

    public function updatedVehiculoSeleccionado(): void
    {
        $this->precio_hora = $this->vehiculo_seleccionado !== null && isset($this->vehiculos_disponibles[$this->vehiculo_seleccionado])
            ? (int) $this->vehiculos_disponibles[$this->vehiculo_seleccionado]['precio']
            : 0;
        $this->recalcular();
    }

    public function updatedFechaInicio(): void
    {
        $this->recalcular();
    }

    public function updatedHoraInicio(): void
    {
        $this->recalcular();
    }

    public function updatedDuracionHoras(): void
    {
        $this->recalcular();
    }

    public function updatedTipoReserva(): void
    {
        $this->recalcular();
    }

    private function recalcular(): void
    {
        $this->total_estimado = $this->precio_hora * max(1, (int) $this->duracion_horas);
        if ($this->fecha_inicio && $this->hora_inicio) {
            $inicio = Carbon::parse($this->fecha_inicio.' '.$this->hora_inicio);
            $fin = $inicio->copy()->addHours(max(1, (int) $this->duracion_horas));
            $this->fecha_fin_estimada = $fin->format('Y-m-d H:i');
        } else {
            $this->fecha_fin_estimada = null;
        }
    }

    public function continuar()
    {
        $this->validate($this->rules(), $this->messages());

        $vehiculo = $this->vehiculos_disponibles[$this->vehiculo_seleccionado] ?? null;
        if (! $vehiculo) {
            $this->addError('vehiculo_seleccionado', 'Selecciona un vehículo');
            return;
        }

        $inicio = Carbon::parse($this->fecha_inicio.' '.$this->hora_inicio);
        $fin = $inicio->copy()->addHours($this->duracion_horas);

        $origen = $this->tipo_reserva === 'punto'
            ? ($this->puntos_disponibles[$this->punto_recogida] ?? 'Sede')
            : ($this->direccion_recogida ?? 'Domicilio');

        // Crear reserva inmediata para llevar datos completos al pago
        $result = $this->reservationService->createReservation([
            'user_id' => Auth::id(),
            'vehiculo_id' => $vehiculo['id'],
            'sede_id' => ($this->tipo_reserva === 'punto') ? $this->punto_recogida : ($vehiculo['sede_id'] ?? null),
            'fecha_inicio' => $inicio->format('Y-m-d H:i'),
            'fecha_fin' => $fin->format('Y-m-d H:i'),
            'origen_direccion' => $origen,
            'destino_direccion' => $origen,
            'notas_cliente' => $this->notas_adicionales,
            'entrega_domicilio' => ($this->tipo_reserva === 'domicilio'),
        ]);

        if (!($result['success'] ?? false)) {
            session()->flash('error', $result['message'] ?? 'No se pudo crear la reserva');
            return;
        }

        $reserva = $result['data'];

        session()->put('reserva_temporal', [
            'id' => $reserva->id,
            'codigo' => $reserva->codigo,
            'vehiculo' => $vehiculo['nombre'],
            'tipo_reserva' => $this->tipo_reserva,
            'fecha_inicio' => $inicio->format('Y-m-d'),
            'hora_inicio' => $inicio->format('H:i'),
            'duracion_horas' => $this->duracion_horas,
            'precio_hora' => $this->precio_hora,
            'total' => (int) ($reserva->monto_final ?? $this->total_estimado),
            'fecha_fin_estimada' => $fin->format('Y-m-d H:i'),
            'direccion_origen' => $origen,
        ]);

        session()->put('reserva_db', [
            'id' => $reserva->id,
            'codigo' => $reserva->codigo,
            'monto_final' => (int) ($reserva->monto_final ?? $this->total_estimado),
        ]);

        $this->redirectRoute('catalog.payment');
        }

    public function rules(): array
    {
        return [
            'vehiculo_seleccionado' => 'required|integer|min:0',
            'tipo_reserva' => 'required|in:punto,domicilio',
            'punto_recogida' => 'required_if:tipo_reserva,punto|nullable|integer',
            'direccion_recogida' => 'required_if:tipo_reserva,domicilio|nullable|string|min:6',
            'fecha_inicio' => 'required|date|after:now',
            'hora_inicio' => 'required',
            'duracion_horas' => 'required|integer|min:1|max:24',
        ];
    }

    public function messages(): array
    {
        return [
            'vehiculo_seleccionado.required' => 'Selecciona un vehículo',
            'punto_recogida.required_if' => 'Selecciona un punto de recogida',
            'direccion_recogida.required_if' => 'Ingresa la dirección de entrega',
            'fecha_inicio.after' => 'La fecha debe ser futura',
        ];
    }

    public function render()
    {
        return view('livewire.formulario-reserva');
    }
}
