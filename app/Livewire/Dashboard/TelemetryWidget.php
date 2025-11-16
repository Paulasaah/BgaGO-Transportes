<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Telemetria;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class TelemetryWidget extends Component
{
    public $stats = [
        'avg_battery' => 0,
        'low_battery_count' => 0,
        'total_km_today' => 0,
        'maintenance_needed' => 0,
        'active_vehicles' => 0,
        'total_vehicles' => 0,
    ];

    public function mount()
    {
        $this->loadTelemetryStats();
    }

    #[On('refreshDashboard')]
    public function loadTelemetryStats()
    {
        $this->stats = Cache::remember('telemetry_stats', 60, function () {
            // Obtener última telemetría de cada dispositivo
            $latestTelemetry = Telemetria::select('telemetrias.*')
                ->join(DB::raw('(SELECT device_id, MAX(created_at) as latest_time 
                                 FROM telemetrias 
                                 GROUP BY device_id) as latest'), function ($join) {
                    $join->on('telemetrias.device_id', '=', 'latest.device_id')
                         ->on('telemetrias.created_at', '=', 'latest.latest_time');
                })
                ->get();

            // Calcular estadísticas
            $avgBattery = $latestTelemetry->avg('battery') ?? 0;
            $lowBatteryCount = $latestTelemetry->where('battery', '<', 20)->count();
            
            // Kilómetros de hoy
            $totalKmToday = Telemetria::whereDate('created_at', today())
                ->sum('odometer') ?? 0;

            // Vehículos que necesitan mantenimiento
            $maintenanceNeeded = $latestTelemetry->filter(function ($t) {
                return $t->needsMaintenance();
            })->count();

            // Vehículos activos (con telemetría en las últimas 2 horas)
            $activeVehicles = Telemetria::where('created_at', '>=', now()->subHours(2))
                ->distinct('device_id')
                ->count('device_id');

            $totalVehicles = Vehicle::count();

            return [
                'avg_battery' => round($avgBattery, 1),
                'low_battery_count' => $lowBatteryCount,
                'total_km_today' => round($totalKmToday, 1),
                'maintenance_needed' => $maintenanceNeeded,
                'active_vehicles' => $activeVehicles,
                'total_vehicles' => $totalVehicles,
            ];
        });
    }

    public function getBatteryColor()
    {
        $battery = $this->stats['avg_battery'];
        if ($battery >= 70) return 'green';
        if ($battery >= 40) return 'yellow';
        return 'red';
    }

    public function getBatteryClasses()
    {
        $color = $this->getBatteryColor();
        
        return [
            'bg' => match($color) {
                'green' => 'bg-green-50 dark:bg-green-900/10',
                'yellow' => 'bg-yellow-50 dark:bg-yellow-900/10',
                'red' => 'bg-red-50 dark:bg-red-900/10',
                default => 'bg-zinc-50 dark:bg-zinc-800/10',
            },
            'border' => match($color) {
                'green' => 'border-green-200 dark:border-green-800',
                'yellow' => 'border-yellow-200 dark:border-yellow-800',
                'red' => 'border-red-200 dark:border-red-800',
                default => 'border-zinc-200 dark:border-zinc-800',
            },
            'icon' => match($color) {
                'green' => 'text-green-600 dark:text-green-400',
                'yellow' => 'text-yellow-600 dark:text-yellow-400',
                'red' => 'text-red-600 dark:text-red-400',
                default => 'text-zinc-600 dark:text-zinc-400',
            },
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.telemetry-widget');
    }
}
