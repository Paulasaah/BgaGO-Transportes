<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class DistribucionChart extends Component
{
    public function getChartData()
    {
        // TODO: Obtener datos reales de la BD
        // Ejemplo:
        /*
        $distribucion = DB::table('reservas')
            ->join('sedes', 'reservas.sede_id', '=', 'sedes.id')
            ->select('sedes.nombre', DB::raw('COUNT(*) as total'))
            ->groupBy('sedes.id', 'sedes.nombre')
            ->get();
        */
        
        return [
            'labels' => ['Sede Norte', 'Sede Sur', 'Sede Centro', 'Sede Oriente'],
            'data' => [35, 28, 22, 15],
            'colors' => [
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ]
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.distribucion-chart', [
            'chartData' => $this->getChartData()
        ]);
    }
}