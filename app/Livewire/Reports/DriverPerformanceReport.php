<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Facades\Data;

class DriverPerformanceReport extends Component
{
    public $conductores = [];
    public $chartData = [];

    public function mount()
    {
        $this->loadReportData();
    }

    public function loadReportData()
    {
        $this->conductores = Data::getDriverPerformance();
        $this->prepareChartData();
    }

    private function prepareChartData()
    {
        if (empty($this->conductores)) {
            $this->chartData = [
                'labels' => [],
                'datasets' => []
            ];
            return;
        }

        // Tomar solo los top 5 conductores para el gráfico radar
        $topConductores = collect($this->conductores)
            ->sortByDesc('servicios_completados')
            ->take(5)
            ->values();

        $labels = $topConductores->pluck('conductor')->toArray();
        $servicios = $topConductores->pluck('servicios_completados')->toArray();
        $calificacion = $topConductores->pluck('calificacion_promedio')->map(fn($c) => $c * 20)->toArray(); // Escalar a 100
        $horas = $topConductores->pluck('horas_trabajo')->toArray();

        $this->chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Servicios',
                    'data' => $servicios,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderWidth' => 2,
                    'pointBackgroundColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => 'Calificación (x20)',
                    'data' => $calificacion,
                    'borderColor' => 'rgb(234, 179, 8)',
                    'backgroundColor' => 'rgba(234, 179, 8, 0.2)',
                    'borderWidth' => 2,
                    'pointBackgroundColor' => 'rgb(234, 179, 8)',
                ]
            ]
        ];
    }

    public function render()
    {
        return view('livewire.reports.driver-performance-report');
    }
}