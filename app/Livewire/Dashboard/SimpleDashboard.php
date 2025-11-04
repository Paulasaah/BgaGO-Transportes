<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Services\MockDataService;

class SimpleDashboard extends Component
{
    public $stats;
    public $reservasLabels;
    public $reservasData;
    public $distribucionLabels;
    public $distribucionData;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Cargar stats
        $this->stats = MockDataService::getDashboardStats();
        
        // Cargar datos de reservas
        $reservas = MockDataService::getReservasPorMes();
        $this->reservasLabels = $reservas['labels'];
        $this->reservasData = $reservas['data'];
        
        // Cargar datos de distribución
        $distribucion = MockDataService::getDistribucionSedes();
        $this->distribucionLabels = $distribucion['labels'];
        $this->distribucionData = $distribucion['data'];
    }

    public function refresh()
    {
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.dashboard.simple-dashboard');
    }
}