<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Facades\Data;

class RevenueReport extends Component
{
    public $periodo = 'mes';
    public $reporte = [];
    public $chartData = [];

    public function mount($periodo = 'mes')
    {
        $this->periodo = $periodo;
        $this->loadReportData();
    }

    public function loadReportData()
    {
        $this->reporte = Data::getRevenueReport($this->periodo);
        
        // Preparar datos para el gráfico
        $this->prepareChartData();
    }

    private function prepareChartData()
    {
        if (empty($this->reporte['por_dia'])) {
            $this->chartData = [
                'labels' => [],
                'datasets' => []
            ];
            return;
        }

        $labels = [];
        $data = [];

        foreach ($this->reporte['por_dia'] as $dia) {
            $labels[] = \Carbon\Carbon::parse($dia['fecha'])->format('d/m');
            $data[] = $dia['monto'];
        }

        $this->chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Ingresos Diarios',
                    'data' => $data,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4
                ]
            ]
        ];
    }

    public function render()
    {
        return view('livewire.reports.revenue-report');
    }
}