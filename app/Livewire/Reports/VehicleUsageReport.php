<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Facades\Data;

class VehicleUsageReport extends Component
{
    public $vehiculos = [];
    public $chartData = [];

    public function mount()
    {
        $this->loadReportData();
    }

    public function loadReportData()
    {
        $this->vehiculos = Data::getVehicleUsageReport();
        $this->prepareChartData();
    }

    private function prepareChartData()
    {
        if (empty($this->vehiculos)) {
            $this->chartData = [
                'labels' => [],
                'datasets' => []
            ];
            return;
        }

        $labels = [];
        $serviciosData = [];
        $horasData = [];

        foreach ($this->vehiculos as $vehiculo) {
            // Extraer solo la placa para el label
            $placa = explode(' - ', $vehiculo['vehiculo'])[0];
            $labels[] = $placa;
            $serviciosData[] = $vehiculo['servicios'];
            $horasData[] = $vehiculo['horas_uso'];
        }

        $this->chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Servicios Completados',
                    'data' => $serviciosData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Horas de Uso',
                    'data' => $horasData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    public function render()
    {
        return view('livewire.reports.vehicle-usage-report');
    }
}