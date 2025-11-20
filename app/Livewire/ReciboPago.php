<?php

namespace App\Livewire;

use App\Enums\PaymentStatus;
use App\Models\Reservation;
use App\Models\Payment;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Livewire\Component;

class ReciboPago extends Component
{
    public ?array $pago = null;

    public ?array $reservaTmp = null;

    public ?array $reservaDb = null;

    protected ReservationService $reservationService;

    public function boot(ReservationService $reservationService): void
    {
        $this->reservationService = $reservationService;
    }

    public function mount(): void
    {
        $this->pago = session('pago_simulado');
        $this->reservaTmp = session('reserva_temporal');

        if (! $this->pago || ! $this->reservaTmp) {
            return;
        }

        if (! session('recibo_persistido')) {
            $inicio = Carbon::parse(($this->reservaTmp['fecha_inicio'] ?? now()->toDateString()).' '.($this->reservaTmp['hora_inicio'] ?? '09:00'));
            $fin = $inicio->copy()->addHours((int) ($this->reservaTmp['duracion_horas'] ?? 1));

            $result = $this->reservationService->createReservation([
                'user_id' => auth()->id(),
                'vehiculo_id' => (int) $this->reservaTmp['vehiculo_id'],
                'sede_id' => (int) ($this->reservaTmp['sede_id'] ?? 0),
                'fecha_inicio' => $inicio,
                'fecha_fin' => $fin,
                'origen_direccion' => $this->reservaTmp['direccion_origen'] ?? 'Sede',
                'destino_direccion' => null,
                'notas_cliente' => $this->reservaTmp['notas'] ?? null,
            ]);

            if (Arr::get($result, 'success', true) === false) {
                $this->dispatch('notify', type: 'error', message: Arr::get($result, 'message', 'Error al crear la reserva'));

                return;
            }

            $reservationData = Arr::get($result, 'data');
            $reservationId = is_array($reservationData) ? ($reservationData['id'] ?? null) : ($reservationData->id ?? null);

            $payment = Payment::create([
                'codigo_transaccion' => $this->pago['transaction_id'] ?? null,
                'reserva_id' => $reservationId,
                'user_id' => auth()->id(),
                'metodo_pago' => 'tarjeta',
                'monto' => (float) (is_array($reservationData) ? ($reservationData['monto_final'] ?? 0) : ($reservationData->monto_final ?? 0)),
                'estado' => PaymentStatus::Pendiente,
                'datos_transaccion' => [
                    'last4' => $this->pago['last4'] ?? null,
                    'method' => $this->pago['method'] ?? 'Tarjeta',
                ],
            ]);

            $payment->approve($this->pago['transaction_id'] ?? null);

            $reservation = Reservation::with('vehicle')->find($reservationId);

            $this->reservaDb = [
                'id' => $reservation->id,
                'codigo' => $reservation->codigo,
                'estado' => 'confirmada',
                'vehiculo' => $reservation->vehicle?->getFullName(),
                'fecha_inicio' => $reservation->fecha_inicio?->format('Y-m-d'),
                'hora_inicio' => $reservation->fecha_inicio?->format('H:i'),
                'duracion_horas' => ceil(($reservation->duracion_minutos ?? 60) / 60),
                'monto' => (float) $reservation->monto,
                'descuento' => (float) ($reservation->descuento ?? 0),
                'monto_final' => (float) $reservation->monto_final,
                'tipo_reserva' => $reservation->destino_direccion ? 'domicilio' : 'sede',
            ];

            $this->pago = [
                'transaction_id' => $payment->codigo_transaccion,
                'method' => $payment->metodo_pago,
                'last4' => Arr::get($payment->datos_transaccion, 'last4'),
                'total' => (float) $reservation->monto_final,
                'timestamp' => $payment->fecha_aprobacion?->format('Y-m-d H:i'),
            ];

            session(['recibo_persistido' => true, 'reserva_db' => $this->reservaDb, 'pago_simulado' => $this->pago]);
        } else {
            $reservaDbSession = session('reserva_db');
            if (is_array($reservaDbSession) && isset($reservaDbSession['id'])) {
                $reservation = Reservation::with('vehicle')->find($reservaDbSession['id']);
                if ($reservation) {
                    $this->reservaDb = [
                        'id' => $reservation->id,
                        'codigo' => $reservation->codigo,
                        'estado' => (string) $reservation->estado->value,
                        'vehiculo' => $reservation->vehicle?->getFullName(),
                        'fecha_inicio' => $reservation->fecha_inicio?->format('Y-m-d'),
                        'hora_inicio' => $reservation->fecha_inicio?->format('H:i'),
                        'duracion_horas' => ceil(($reservation->duracion_minutos ?? 60) / 60),
                        'monto' => (float) $reservation->monto,
                        'descuento' => (float) ($reservation->descuento ?? 0),
                        'monto_final' => (float) $reservation->monto_final,
                        'tipo_reserva' => $reservation->destino_direccion ? 'domicilio' : 'sede',
                    ];
                }
            } else {
                $this->reservaDb = $reservaDbSession;
            }
            $this->pago = session('pago_simulado') ?? $this->pago;
        }
    }

    public function render()
    {
        return view('livewire.recibo-pago');
    }
}
