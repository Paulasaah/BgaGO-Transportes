<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Facades\Data;

class RevenueReport extends Component
{
    public $periodo = 'mes';
    public $ingresos = [];
    public $chartData = [];

    public function mount($periodo = 'mes')
    {
        $this->periodo = $periodo;
        $this->loadData();
    }

    public function loadData()
    {
        $this->ingresos = Data::getRevenueReport($this->periodo);
        
        // Preparar datos para Chart.js
        $this->chartData = [
            'labels' => array_map(function($item) {
                return \Carbon\Carbon::parse($item['fecha'])->format('d/m');
            }, $this->ingresos['por_dia'] ?? []),
            'datasets' => [
                [
                    'label' => 'Ingresos Diarios',
                    'data' => array_column($this->ingresos['por_dia'] ?? [], 'monto'),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ]
            ]
        ];
    }

    public function render()
    {
        return view('livewire.reports.revenue-report');
    }
}