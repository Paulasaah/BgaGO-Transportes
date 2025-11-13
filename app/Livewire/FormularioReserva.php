<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Carbon\Carbon;
use App\Models\Branch;
use Illuminate\Support\Str;

class FormularioReserva extends Component
{
    public $vehiculo_seleccionado = '';
    public $precio_hora = 0;

    #[Validate('required|string')]
    public $tipo_reserva = 'punto';

    #[Validate('required_if:tipo_reserva,punto|string')]
    public $punto_recogida = '';

    #[Validate('required_if:tipo_reserva,domicilio|string|max:255')]
    public $direccion_recogida = '';

    #[Validate('required|date|after_or_equal:today')]
    public $fecha_inicio = '';

    #[Validate('required|date_format:H:i')]
    public $hora_inicio = '';

    #[Validate('required|integer|min:1|max:24')]
    public $duracion_horas = 1;

    #[Validate('nullable|string|max:500')]
    public $notas_adicionales = '';

    public $total_estimado = 0;
    public $fecha_fin_estimada = '';

    // ⭐ ESTAS VARIABLES SON LAS QUE FALTABAN ⭐
    public $vehiculos_disponibles = [];
    public $puntos_disponibles = [];

    public function mount()
    {
        // ⭐ INICIALIZAR VEHÍCULOS DISPONIBLES ⭐
        $this->vehiculos_disponibles = [
            'bicicleta-electrica' => ['nombre' => 'Bicicleta Eléctrica', 'precio' => 5000],
            'moto-electrica' => ['nombre' => 'Moto Eléctrica', 'precio' => 15000],
            'patineta-electrica' => ['nombre' => 'Patineta Eléctrica', 'precio' => 3500],
            'bicicleta-manual' => ['nombre' => 'Bicicleta Manual', 'precio' => 2500],
        // 'patines-linea' eliminado para revertir tipos a versión previa
        ];

        // ⭐ INICIALIZAR PUNTOS DE RECOGIDA DESDE BD (sólo sedes existentes) ⭐
        $this->puntos_disponibles = Branch::query()
            ->get()
            ->mapWithKeys(function ($b) {
                return [Str::slug($b->nombre) => $b->nombre . ' - ' . $b->direccion];
            })
            ->toArray();

        // ⭐⭐⭐ CAPTURAR VEHÍCULO DESDE LA URL ⭐⭐⭐
        $vehiculo = request()->query('vehiculo', '');
        $precio = request()->query('precio', 0);

        if ($vehiculo && isset($this->vehiculos_disponibles[$vehiculo])) {
            $this->vehiculo_seleccionado = $vehiculo;
            $this->precio_hora = $this->vehiculos_disponibles[$vehiculo]['precio'];
        } elseif ($precio > 0) {
            $this->precio_hora = $precio;
        }

        // Establecer fecha mínima (hoy + 1 hora)
        $this->fecha_inicio = Carbon::now()->addHour()->format('Y-m-d');
        $this->hora_inicio = Carbon::now()->addHour()->format('H:i');

        $this->calcularTotal();
    }

    public function updatedVehiculoSeleccionado($value)
    {
        if (isset($this->vehiculos_disponibles[$value])) {
            $this->precio_hora = $this->vehiculos_disponibles[$value]['precio'];
            $this->calcularTotal();
        }
    }

    public function updatedDuracionHoras()
    {
        $this->duracion_horas = max(1, min(24, (int) $this->duracion_horas));
        $this->calcularTotal();
    }

    public function updatedFechaInicio()
    {
        $this->calcularTotal();
    }

    public function updatedHoraInicio()
    {
        $this->calcularTotal();
    }

    public function calcularTotal()
    {
        $horas = (int) $this->duracion_horas;
        $this->total_estimado = $this->precio_hora * $horas;

        if ($this->fecha_inicio && $this->hora_inicio) {
            try {
                $inicio = Carbon::createFromFormat('Y-m-d H:i', $this->fecha_inicio . ' ' . $this->hora_inicio, config('app.timezone'));
                $fin = $inicio->copy()->addHours($horas);
                $this->fecha_fin_estimada = $fin->format('d/m/Y H:i');
            } catch (\Exception $e) {
                $this->fecha_fin_estimada = '';
            }
        }
    }

    public function continuar()
    {
        $this->validate();

        try {
            $tz = config('app.timezone');
            $inicio = Carbon::createFromFormat('Y-m-d H:i', $this->fecha_inicio . ' ' . $this->hora_inicio, $tz);
            $ahoraMasUnaHora = Carbon::now($tz)->addHours(1);
            if ($inicio->lte($ahoraMasUnaHora)) {
                $this->addError('fecha_inicio', 'La reserva debe ser al menos 1 hora en el futuro.');
                return;
            }
        } catch (\Exception $e) {
            $this->addError('fecha_inicio', 'Fecha u hora inválida.');
            return;
        }

        session([
            'reserva_temporal' => [
                'vehiculo' => $this->vehiculo_seleccionado,
                'tipo_reserva' => $this->tipo_reserva,
                'punto_recogida' => $this->punto_recogida,
                'direccion_recogida' => $this->direccion_recogida,
                'fecha_inicio' => $this->fecha_inicio,
                'hora_inicio' => $this->hora_inicio,
                'duracion_horas' => $this->duracion_horas,
                'notas_adicionales' => $this->notas_adicionales,
                'total' => $this->total_estimado,
                'fecha_fin' => $this->fecha_fin_estimada,
            ]
        ]);

        return $this->redirect(route('pago', ['reserva' => 'nueva']), navigate: false);
    }

    public function render()
    {
        return view('livewire.formulario-reserva');
    }
}
