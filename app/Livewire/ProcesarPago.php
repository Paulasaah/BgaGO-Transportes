<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use App\Services\ReservationService;
use App\Models\Reservation;
use App\Enums\ReservationStatus;
use Carbon\Carbon;

class ProcesarPago extends Component
{
    public array $metodos = [];
    public string $metodo_seleccionado = 'tarjeta';

    public ?string $numero_tarjeta = null;
    public ?string $fecha_expiracion = null;
    public ?string $nombre_titular = null;
    public ?string $cvv = null;
    public string $tipo_tarjeta = 'credito';

    public bool $acepta_terminos = false;

    public array $wallet_cards = [];

    protected ReservationService $reservationService;

    public function boot(ReservationService $reservationService): void
    {
        $this->reservationService = $reservationService;
    }

    public function mount(): void
    {
        $this->metodos = [
            ['tipo' => 'tarjeta', 'nombre' => 'Tarjeta', 'descripcion' => 'Visa, Mastercard'],
            ['tipo' => 'wallet', 'nombre' => 'Wallet', 'descripcion' => 'Usa tus tarjetas guardadas'],
            ['tipo' => 'mercadopago', 'nombre' => 'Mercado Pago', 'descripcion' => 'Paga con tu cuenta MP'],
            ['tipo' => 'transferencia', 'nombre' => 'Transferencia', 'descripcion' => 'PSE/Transferencia bancaria'],
            ['tipo' => 'efectivo', 'nombre' => 'Efectivo', 'descripcion' => 'Pago al recoger (solo sedes)'],
        ];

        $saved = session('wallet_cards_' . Auth::id());
        if (is_array($saved)) {
            $this->wallet_cards = $saved;
        }

        $pref = session('wallet_preferred_' . Auth::id());
        if (is_numeric($pref) && isset($this->wallet_cards[(int) $pref])) {
            $card = $this->wallet_cards[(int) $pref];
            $this->metodo_seleccionado = 'tarjeta';
            $this->numero_tarjeta = $card['numero'] ?? null;
            $this->nombre_titular = $card['nombre'] ?? null;
            $this->fecha_expiracion = $card['exp'] ?? null;
            $this->tipo_tarjeta = $card['tipo'] ?? 'credito';
        }
    }

    public function rules(): array
    {
        return [
            'metodo_seleccionado' => 'required|in:tarjeta,wallet,mercadopago,transferencia,efectivo',
            'numero_tarjeta' => 'required_if:metodo_seleccionado,tarjeta|nullable|string|min:12|max:19',
            'fecha_expiracion' => 'required_if:metodo_seleccionado,tarjeta|nullable|string|size:5',
            'nombre_titular' => 'required_if:metodo_seleccionado,tarjeta|nullable|string|min:3',
            'cvv' => 'required_if:metodo_seleccionado,tarjeta|nullable|digits_between:3,4',
            'tipo_tarjeta' => 'required_if:metodo_seleccionado,tarjeta|nullable|in:credito,debito',
            'acepta_terminos' => 'accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'acepta_terminos.accepted' => 'Debes aceptar términos y políticas',
        ];
    }

    public function walletUseCard(int $index): void
    {
        if (!isset($this->wallet_cards[$index])) {
            return;
        }
        $card = $this->wallet_cards[$index];
        $this->metodo_seleccionado = 'tarjeta';
        $this->numero_tarjeta = $card['numero'] ?? null;
        $this->nombre_titular = $card['nombre'] ?? null;
        $this->fecha_expiracion = $card['exp'] ?? null;
        $this->tipo_tarjeta = $card['tipo'] ?? 'credito';
    }

    public function procesarPago(): void
    {
        $this->validate();

        $reservaTmp = session('reserva_temporal');
        $reservaDb = session('reserva_db');

        if (!$reservaDb && $reservaTmp) {
            if (isset($reservaTmp['id'])) {
                $existing = Reservation::find($reservaTmp['id']);
                if ($existing) {
                    session()->put('reserva_db', [
                        'id' => $existing->id,
                        'codigo' => $existing->codigo,
                        'monto_final' => (int) ($existing->monto_final ?? 0),
                    ]);
                }
            } else {
                $inicioStr = trim(($reservaTmp['fecha_inicio'] ?? '') . ' ' . ($reservaTmp['hora_inicio'] ?? ''));
                $finStr = $reservaTmp['fecha_fin_estimada'] ?? null;
                if (!$finStr && ($reservaTmp['duracion_horas'] ?? null)) {
                    $finStr = Carbon::parse($inicioStr)->addHours((int) $reservaTmp['duracion_horas'])->format('Y-m-d H:i');
                }

                // Intentar recuperar una reserva pendiente existente del usuario
                if (!($reservaTmp['vehiculo_id'] ?? null)) {
                    $existingPending = Reservation::where('user_id', Auth::id())
                        ->where('estado', ReservationStatus::Pendiente)
                        ->orderByDesc('created_at')
                        ->first();
                    if ($existingPending) {
                        session()->put('reserva_db', [
                            'id' => $existingPending->id,
                            'codigo' => $existingPending->codigo,
                            'monto_final' => (int) ($existingPending->monto_final ?? 0),
                        ]);
                        goto after_reserva_db_set;
                    }
                }

                $payload = [
                    'user_id' => Auth::id(),
                    'vehiculo_id' => $reservaTmp['vehiculo_id'] ?? null,
                    'sede_id' => $reservaTmp['sede_id'] ?? null,
                    'fecha_inicio' => $inicioStr,
                    'fecha_fin' => $finStr,
                    'origen_direccion' => $reservaTmp['direccion_origen'] ?? 'Sede',
                    'entrega_domicilio' => ($reservaTmp['tipo_reserva'] ?? '') === 'domicilio',
                    'notas_cliente' => null,
                ];

                if (!($payload['vehiculo_id'] ?? null)) {
                    session()->flash('error', "Falta el vehículo para crear la reserva. Por favor selecciona uno.");
                    return;
                }

                $result = $this->reservationService->createReservation($payload);
                if (!($result['success'] ?? false)) {
                    session()->flash('error', $result['message'] ?? 'No se pudo crear la reserva');
                    return;
                }
                $reserva = $result['data'];
                session()->put('reserva_db', [
                    'id' => $reserva->id,
                    'codigo' => $reserva->codigo,
                    'monto_final' => (int) ($reserva->monto_final ?? 0),
                ]);
            }
        }

        after_reserva_db_set:
        $reservaDb = session('reserva_db');

        if (!$reservaDb) {
            session()->flash('error', 'No hay reserva para procesar el pago');
            return;
        }

        if ($this->metodo_seleccionado === 'wallet') {
            session()->flash('error', 'Selecciona una tarjeta guardada para continuar');
            return;
        }

        $total = (int) ($reservaDb['monto_final'] ?? ($reservaTmp['total'] ?? 0));

        $payment = Payment::create([
            'reserva_id' => $reservaDb['id'],
            'user_id' => Auth::id(),
            'metodo_pago' => $this->metodo_seleccionado,
            'monto' => $total,
            'estado' => PaymentStatus::Pendiente,
        ]);

        $payment->approve('SIM-OK');

        $this->reservationService->confirmReservation($reservaDb['id']);

        $digits = preg_replace('/\D/', '', (string) $this->numero_tarjeta);
        $last4 = $digits ? substr($digits, -4) : '0000';

        session()->put('pago_exitoso', [
            'pago_id' => $payment->codigo_transaccion,
            'fecha' => now()->toIso8601String(),
            'metodo' => ucfirst($this->metodo_seleccionado),
            'ultimos_digitos' => $last4,
            'monto' => $total,
            'reserva' => $reservaTmp ?? $reservaDb,
        ]);

        $this->redirect(route('catalog.confirmation', absolute: false), navigate: true);
    }

    public function render()
    {
        return view('livewire.procesar-pago');
    }
}
