<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Facades\Data; // 👈 Importa el Facade que conecta con tu DataService real

class SimpleDashboard extends Component
{
    public $stats = [];
    public $reservasLabels = [];
    public $reservasData = [];
    public $distribucionLabels = [];
    public $distribucionData = [];

    public function mount()
    {
        // ✅ Usamos Data:: en lugar de MockDataService::
        $this->stats = Data::getDashboardStats();
        $reservas = Data::getReservasPorMes();
        $distribucion = Data::getDistribucionSedes();

        $this->reservasLabels = $reservas['labels'] ?? [];
        $this->reservasData = $reservas['data'] ?? [];

        $this->distribucionLabels = $distribucion['labels'] ?? [];
        $this->distribucionData = $distribucion['data'] ?? [];
    }

    public function render()
    {
        return view('livewire.dashboard.simple-dashboard');
    }
}
