<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Facades\Data;

class SimpleDashboard extends Component
{
    public $stats = [];
    public $reservasLabels = [];
    public $reservasData = [];
    public $distribucionLabels = [];
    public $distribucionData = [];

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        // Obtener stats con valores por defecto
        $this->stats = Data::getDashboardStats() ?: $this->getDefaultStats();
        $reservas = Data::getReservasPorMes() ?: ['labels' => [], 'data' => []];
        $distribucion = Data::getDistribucionSedes() ?: ['labels' => [], 'data' => []];

        $this->reservasLabels = $reservas['labels'] ?? [];
        $this->reservasData = $reservas['data'] ?? [];

        $this->distribucionLabels = $distribucion['labels'] ?? [];
        $this->distribucionData = $distribucion['data'] ?? [];
    }

    /**
     * Obtener estadísticas por defecto cuando el servicio falla
     */
    private function getDefaultStats(): array
    {
        return [
            'reservas_activas' => [
                'value' => 0,
                'change' => '+0',
                'change_text' => 'desde ayer',
                'change_type' => 'neutral'
            ],
            'domicilios_hoy' => [
                'value' => 0,
                'change' => '+0',
                'change_text' => 'en progreso',
                'change_type' => 'neutral'
            ],
            'ingresos_mes' => [
                'value' => '$0.0M',
                'change' => '+0%',
                'change_text' => 'vs mes anterior',
                'change_type' => 'neutral'
            ],
            'en_mantenimiento' => [
                'value' => 0,
                'change' => 0,
                'change_text' => 'requieren atención',
                'change_type' => 'neutral'
            ]
        ];
    }

    public function refresh()
    {
        $this->loadDashboardData();
        $this->dispatch('refreshDashboard');
        
        $this->dispatch('showNotification', 
            message: 'Dashboard actualizado', 
            type: 'success'
        );
    }

    public function render()
    {
        return view('livewire.dashboard.simple-dashboard');
    }
}
