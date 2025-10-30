<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class StatsCards extends Component
{
    public $reservasActivas;
    public $domiciliosHoy;
    public $ingresosMes;
    public $mediosMantenimiento;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        // TODO: Obtener datos reales de la BD
        /*
        $this->reservasActivas = Reserva::where('estado', 'activa')->count();
        $this->domiciliosHoy = Domicilio::whereDate('fecha', today())->count();
        $this->ingresosMes = Pago::whereMonth('created_at', now()->month)->sum('monto');
        $this->mediosMantenimiento = Medio::where('estado', 'mantenimiento')->count();
        */
        
        // Datos de ejemplo
        $this->reservasActivas = 24;
        $this->domiciliosHoy = 18;
        $this->ingresosMes = 4200000;
        $this->mediosMantenimiento = 5;
    }

    public function render()
    {
        return view('livewire.dashboard.stats-cards');
    }
}