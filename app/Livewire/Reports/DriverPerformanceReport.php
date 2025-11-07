<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Facades\Data;

class DriverPerformanceReport extends Component
{
    public $periodo = 'mes';
    public $conductores = [];
    public $chartData = [];

    public function mount($periodo = 'mes')
    {
        $this->periodo = $periodo;
        $this->loadData();
    }

    public function loadData()
    {
        $this->conductores = Data::getDriverPerformance();
        
        // Preparar datos para Chart.js - Gráfico de Radar
        $this->chartData = [
            'labels' => array_map(function($nombre) {
                // Acortar nombres largos
                return strlen($nombre) > 15 ? substr($nombre, 0, 15) . '...' : $nombre;
            }, array_column($this->conductores, 'conductor')),
            'datasets' => [
                [
                    'label' => 'Calificación (x20)',
                    'data' => array_map(fn($cal) => $cal * 20, array_column($this->conductores, 'calificacion_promedio')),
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                    'borderColor' => 'rgba(245, 158, 11, 1)',
                    'pointBackgroundColor' => 'rgba(245, 158, 11, 1)',
                ],
                [
                    'label' => 'Servicios Completados',
                    'data' => array_column($this->conductores, 'servicios_completados'),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'pointBackgroundColor' => 'rgba(59, 130, 246, 1)',
                ]
            ]
        ];
    }

    public function render()
    {
        return view('livewire.reports.driver-performance-report');
    }
}