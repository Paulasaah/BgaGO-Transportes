<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Carbon\Carbon;

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

    #[Validate('required|date|after:now')]
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
            'patines-linea' => ['nombre' => 'Patines en Línea', 'precio' => 4000],
        ];

        // ⭐ INICIALIZAR PUNTOS DE RECOGIDA ⭐
        $this->puntos_disponibles = [
            'cabecera' => 'Cabecera del Llano - Calle 42 #20-10',
            'canaveral' => 'Cañaveral - Cra. 27 #123-45',
            'piedecuesta' => 'Piedecuesta - Calle 10 #15-30',
            'floridablanca' => 'Floridablanca - Calle 52 #5-80'
        ];

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
        $this->total_estimado = $this->precio_hora * $this->duracion_horas;

        if ($this->fecha_inicio && $this->hora_inicio) {
            try {
                $inicio = Carbon::parse($this->fecha_inicio . ' ' . $this->hora_inicio);
                $fin = $inicio->copy()->addHours($this->duracion_horas);
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
            $inicio = Carbon::parse($this->fecha_inicio . ' ' . $this->hora_inicio);
            if ($inicio->isPast() || $inicio->diffInMinutes(Carbon::now()) < 60) {
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

        return $this->redirect(route('pago', ['reserva' => 'nueva']), navigate: true);
    }

    public function render()
    {
        return view('livewire.formulario-reserva');
    }
}
