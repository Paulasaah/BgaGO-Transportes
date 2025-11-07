<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Facades\Data;

class VehicleUsageReport extends Component
{
    public $periodo = 'mes';
    public $vehiculos = [];
    public $chartData = [];

    public function mount($periodo = 'mes')
    {
        $this->periodo = $periodo;
        $this->loadData();
    }

    public function loadData()
    {
        $this->vehiculos = Data::getVehicleUsageReport();
        
        // Preparar datos para Chart.js
        $this->chartData = [
            'labels' => array_column($this->vehiculos, 'vehiculo'),
            'datasets' => [
                [
                    'label' => 'Servicios Completados',
                    'data' => array_column($this->vehiculos, 'servicios'),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                ],
                [
                    'label' => 'Horas de Uso',
                    'data' => array_column($this->vehiculos, 'horas_uso'),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                ]
            ]
        ];
    }

    public function render()
    {
        return view('livewire.reports.vehicle-usage-report');
    }
}