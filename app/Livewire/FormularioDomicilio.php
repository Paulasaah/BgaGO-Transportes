<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;

class FormularioDomicilio extends Component
{
    #[Validate('required|string|max:255')]
    public $direccion_origen = '';

    #[Validate('required|string|max:255')]
    public $direccion_destino = '';

    #[Validate('required|string|max:500')]
    public $descripcion_paquete = '';

    #[Validate('nullable|string|max:255')]
    public $telefono_destinatario = '';

    #[Validate('nullable|string|max:255')]
    public $nombre_destinatario = '';

    #[Validate('required|in:pequeno,mediano,grande')]
    public $tamano_paquete = 'pequeno';

    public $requiere_seguro = false;

    public $tarifa_estimada = 0;
    public $distancia_estimada = 0;

    public function mount()
    {
        // Inicializar valores por defecto
        $this->calcularTarifa();
    }

    public function updatedDireccionOrigen()
    {
        $this->calcularTarifa();
    }

    public function updatedDireccionDestino()
    {
        $this->calcularTarifa();
    }

    public function updatedTamanoPaquete()
    {
        $this->calcularTarifa();
    }

    public function calcularTarifa()
    {
        // Simulación de cálculo de tarifa (en producción esto se haría con API de mapas)
        if (!empty($this->direccion_origen) && !empty($this->direccion_destino)) {
            // Simulamos una distancia aleatoria entre 1 y 15 km
            $this->distancia_estimada = rand(1, 15);

            // Cálculo base según distancia
            if ($this->distancia_estimada <= 2) {
                $this->tarifa_estimada = 3000;
            } elseif ($this->distancia_estimada <= 5) {
                $this->tarifa_estimada = 5000;
            } elseif ($this->distancia_estimada <= 10) {
                $this->tarifa_estimada = 8000;
            } else {
                $this->tarifa_estimada = 10000 + (($this->distancia_estimada - 10) * 1000);
            }

            // Ajuste por tamaño del paquete
            $multiplicadores = [
                'pequeno' => 1,
                'mediano' => 1.3,
                'grande' => 1.5
            ];

            $this->tarifa_estimada *= ($multiplicadores[$this->tamano_paquete] ?? 1);

            // Redondear
            $this->tarifa_estimada = round($this->tarifa_estimada, -2);
        } else {
            // Si no hay direcciones, resetear valores
            $this->tarifa_estimada = 0;
            $this->distancia_estimada = 0;
        }
    }

    public function solicitar()
    {
        $this->validate();

        // Simular creación de una reserva temporal para el flujo de pago
        $reservaTemporal = [
            'vehiculo' => 'servicio-domicilio',
            'tipo_reserva' => 'domicilio',
            'fecha_inicio' => now()->format('Y-m-d'),
            'hora_inicio' => now()->format('H:i'),
            'duracion_horas' => 1,
            'total' => max(0, (int) ($this->tarifa_estimada + ($this->requiere_seguro ? 2000 : 0))),
            'origen' => $this->direccion_origen,
            'destino' => $this->direccion_destino,
            'tamano_paquete' => $this->tamano_paquete,
            'requiere_seguro' => (bool) $this->requiere_seguro,
            'descripcion_paquete' => $this->descripcion_paquete,
            'distancia_estimada' => $this->distancia_estimada,
        ];

        session()->put('reserva_temporal', $reservaTemporal);

        // Redireccionar al View de pagos sin usar navegación de Alpine
        return $this->redirect(route('pago', ['reserva' => 'domicilio']), navigate: false);
    }

    public function render()
    {
        return view('livewire.formulario-domicilio');
    }
}
