<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ReservasChart extends Component
{
    public function getChartData()
    {
        // TODO: Aquí obtendrías los datos reales de tu BD
        // Ejemplo simulado:
        /*
        $reservas = DB::table('reservas')
            ->select(DB::raw('MONTH(created_at) as mes'), DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('mes')
            ->get();
        */
        
        // Por ahora datos de ejemplo
        return [
            'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            'data' => [65, 78, 90, 81, 95, 103, 110, 98, 115, 122, 130, 140]
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.reservas-chart', [
            'chartData' => $this->getChartData()
        ]);
    }
}