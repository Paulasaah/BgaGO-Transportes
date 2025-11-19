<?php

namespace App\Livewire;

use App\Enums\PaymentStatus;
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

            $reservation = Arr::get($result, 'data');

            $payment = Payment::create([
                'codigo_transaccion' => $this->pago['transaction_id'] ?? null,
                'reserva_id' => $reservation->id,
                'user_id' => auth()->id(),
                'metodo_pago' => 'tarjeta',
                'monto' => (float) ($this->pago['total'] ?? 0),
                'estado' => PaymentStatus::Pendiente,
                'datos_transaccion' => [
                    'last4' => $this->pago['last4'] ?? null,
                    'method' => $this->pago['method'] ?? 'Tarjeta',
                ],
            ]);

            $payment->approve($this->pago['transaction_id'] ?? null);

            $this->reservaDb = [
                'id' => $reservation->id,
                'codigo' => $reservation->codigo,
                'estado' => 'confirmada',
            ];

            session(['recibo_persistido' => true, 'reserva_db' => $this->reservaDb]);
        } else {
            $this->reservaDb = session('reserva_db');
        }
    }

    public function render()
    {
        return view('livewire.recibo-pago');
    }
}
