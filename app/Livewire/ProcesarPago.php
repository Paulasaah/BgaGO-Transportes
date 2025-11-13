<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\PaymentMethod;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Branch;
use App\Enums\ReservationType;
use App\Enums\ReservationStatus;
use App\Enums\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProcesarPago extends Component
{
    #[Validate('required|string|min:16|max:19')]
    public $numero_tarjeta = '';

    #[Validate('required|string|max:100')]
    public $nombre_titular = '';

    #[Validate('required|string|size:5')]
    public $fecha_expiracion = ''; // MM/YY

    #[Validate('required|string|min:3|max:4')]
    public $cvv = '';

    #[Validate('required|in:credito,debito')]
    public $tipo_tarjeta = 'credito';

    public $procesando = false;

    public array $metodos = [];
    public ?string $metodo_seleccionado = null;

    public function mount(): void
    {
        $this->metodos = PaymentMethod::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get()
            ->toArray();

        $this->metodo_seleccionado = collect($this->metodos)->first()['tipo'] ?? 'tarjeta';
    }

    public function updatedNumeroTarjeta()
    {
        // Eliminar espacios y guiones
        $numero = preg_replace('/[^0-9]/', '', $this->numero_tarjeta);

        // Formatear con espacios cada 4 dígitos
        if (strlen($numero) > 0) {
            $partes = str_split($numero, 4);
            $this->numero_tarjeta = implode(' ', $partes);
        }
    }

    public function updatedFechaExpiracion()
    {
        // Eliminar todo excepto números
        $fecha = preg_replace('/[^0-9]/', '', $this->fecha_expiracion);

        // Formatear como MM/YY
        if (strlen($fecha) >= 2) {
            $this->fecha_expiracion = substr($fecha, 0, 2);
            if (strlen($fecha) > 2) {
                $this->fecha_expiracion .= '/' . substr($fecha, 2, 2);
            }
        }
    }

    public function procesarPago()
    {
        $tipos = array_column($this->metodos, 'tipo');

        if ($this->metodo_seleccionado === 'tarjeta') {
            $rules = [
                'numero_tarjeta' => 'required|string|min:16|max:19',
                'nombre_titular' => 'required|string|max:100',
                'fecha_expiracion' => 'required|string|size:5',
                'cvv' => 'required|string|min:3|max:4',
                'tipo_tarjeta' => 'required|in:credito,debito',
            ];
        } else {
            $rules = [
                'metodo_seleccionado' => 'required|in:' . implode(',', $tipos),
            ];
        }

        $this->validate($rules);

        $this->procesando = true;

        $reserva = session('reserva_temporal');

        if (!$reserva) {
            session()->flash('error', 'No hay reserva activa para procesar.');
            $this->procesando = false;
            return;
        }

        // Simular delay de procesamiento
        sleep(2);

        // Calcular total
        $total = $reserva['total'] + ($reserva['tipo_reserva'] == 'domicilio' ? 5000 : 0);

        // Generar ID de pago simulado
        $pago_id = 'PAY-' . strtoupper(uniqid());

        $metodo = collect($this->metodos)->firstWhere('tipo', $this->metodo_seleccionado);
        $metodoNombre = $this->metodo_seleccionado === 'tarjeta'
            ? 'Tarjeta ' . ucfirst($this->tipo_tarjeta)
            : ($metodo['nombre'] ?? ucfirst($this->metodo_seleccionado));

        // Persistir reserva
        $inicio = Carbon::parse($reserva['fecha_inicio'] . ' ' . $reserva['hora_inicio']);
        $fin = $inicio->copy()->addHours((int) ($reserva['duracion_horas'] ?? 1));

        $sedeId = null;
        $key = $reserva['punto_recogida'] ?? null;
        if (($reserva['tipo_reserva'] ?? 'punto') === 'punto' && $key) {
            $branch = Branch::all()->first(function ($b) use ($key) {
                return \Illuminate\Support\Str::slug($b->nombre) === $key;
            });
            $sedeId = $branch?->id;
        }
        // Fallback: si no hay sede encontrada o es domicilio, usar la primera sede disponible para cumplir la restricción NOT NULL
        if (!$sedeId) {
            $sedeId = Branch::query()->value('id');
        }

        $branch = Branch::find($sedeId);
        $branchAddr = $branch?->direccion ?? 'Sede';

        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'sede_id' => $sedeId,
            'tipo' => ($reserva['tipo_reserva'] === 'domicilio') ? ReservationType::Domicilio : ReservationType::Reserva,
            'estado' => ReservationStatus::Confirmada,
            'origen_direccion' => $branchAddr,
            'destino_direccion' => $reserva['tipo_reserva'] === 'domicilio'
                ? ($reserva['direccion_recogida'] ?? $branchAddr)
                : $branchAddr,
            'fecha_inicio' => $inicio,
            'fecha_fin' => $fin,
            'fecha_confirmacion' => now(),
            'monto' => $reserva['total'],
            'descuento' => 0,
            'monto_final' => $total,
            'notas_cliente' => $reserva['notas_adicionales'] ?? null,
            'duracion_minutos' => ($reserva['duracion_horas'] ?? 1) * 60,
        ]);

        // Persistir pago
        $payment = Payment::create([
            'reserva_id' => $reservation->id,
            'user_id' => Auth::id(),
            'metodo_pago' => $this->metodo_seleccionado,
            'monto' => $total,
            'estado' => PaymentStatus::Aprobado,
            'referencia_externa' => $pago_id,
            'datos_transaccion' => [
                'metodo' => $metodoNombre,
                'tipo_tarjeta' => $this->metodo_seleccionado === 'tarjeta' ? $this->tipo_tarjeta : null,
                'ultimos_digitos' => $this->metodo_seleccionado === 'tarjeta'
                    ? substr(str_replace(' ', '', $this->numero_tarjeta), -4)
                    : null,
            ],
            'fecha_aprobacion' => now(),
        ]);

        session([
            'pago_exitoso' => [
                'pago_id' => $pago_id,
                'monto' => $total,
                'fecha' => now()->format('Y-m-d H:i:s'),
                'metodo' => $metodoNombre,
                'ultimos_digitos' => $this->metodo_seleccionado === 'tarjeta'
                    ? substr(str_replace(' ', '', $this->numero_tarjeta), -4)
                    : null,
                'reserva' => $reserva,
            ]
        ]);

        session()->forget('reserva_temporal');

        return $this->redirect(route('confirmacion-pago', ['pago' => $pago_id]), navigate: false);
    }

    public function render()
    {
        return view('livewire.procesar-pago');
    }
}
