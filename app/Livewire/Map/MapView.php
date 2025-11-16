<?php

namespace App\Livewire\Map;

use Livewire\Component;
use App\Models\Telemetria;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MapView extends Component
{
    public $vehicles = [];
    public $branches = [];
    public $autoRefresh = false; // Deshabilitado, usamos broadcasting
    public $refreshInterval = 10; // Fallback si broadcasting falla

    public $filterType = 'all';
    public $filterStatus = 'all';
    public $showAlerts = true;

    public $stats = [
        'total_devices' => 0,
        'active_vehicles' => 0,
        'active_conductors' => 0,
        'charging' => 0,
        'maintenance_needed' => 0,
        'avg_battery' => 0,
    ];

    public function mount()
    {
        $this->loadMapData();
    }

    public function loadMapData()
    {
        $cacheKey = "map_data_{$this->filterType}_{$this->filterStatus}";

        $latestData = Cache::remember($cacheKey, 2, function () {
            return Telemetria::select('telemetrias.*')
                ->join(DB::raw('(SELECT device_id, MAX(created_at) as latest_time 
                                 FROM telemetrias 
                                 GROUP BY device_id) as latest'), function ($join) {
                    $join->on('telemetrias.device_id', '=', 'latest.device_id')
                         ->on('telemetrias.created_at', '=', 'latest.latest_time');
                })
                ->when($this->filterType !== 'all', fn($q) => $q->where('telemetrias.device_type', $this->filterType))
                ->when($this->filterStatus !== 'all', fn($q) => $q->where('telemetrias.status', $this->filterStatus))
                ->orderBy('telemetrias.device_id')
                ->get();
        });

        $this->calculateStats($latestData);

        $this->vehicles = $latestData->map(function ($latest) {
            return [
                'device_id' => $latest->device_id,
                'type' => $latest->device_type,
                'status' => $latest->status,
                'status_label' => $this->getStatusLabel($latest->status),
                'lat' => (float) $latest->lat,
                'lng' => (float) $latest->lon,
                'battery' => (float) $latest->battery,
                'battery_health' => (float) $latest->battery_health,
                'speed' => (float) ($latest->speed ?? 0),
                'current_branch' => $latest->current_branch,
                'target_branch' => $latest->target_branch,
                'odometer' => (float) $latest->odometer,
                'trip_count' => (int) $latest->trip_count,
                'maintenance_km_left' => (float) $latest->maintenance_km_left,
                'needs_maintenance' => $latest->needsMaintenance(),
                'driver_name' => $latest->driver_name,
                'deliveries_completed' => (int) $latest->deliveries_completed,
                'rating' => (float) ($latest->rating ?? 0),
                'has_route' => false,
                'route' => [],
                'updated_at' => $latest->updated_at->diffForHumans(),
            ];
        })->values()->toArray();

        $this->branches = Branch::all()->map(function ($branch) {
            $devicesInBranch = collect($this->vehicles)
                ->where('current_branch', $branch->nombre)
                ->count();

            return [
                'id' => $branch->id,
                'nombre' => $branch->nombre,
                'lat' => (float) $branch->lat,
                'lng' => (float) $branch->lon,
                'radio' => (int) ($branch->radio ?? 500),
                'color' => $branch->color ?? '#3b82f6',
                'descripcion' => $branch->descripcion ?? '',
                'capacidad' => (int) ($branch->capacidad_vehiculos ?? 10),
                'dispositivos_actuales' => $devicesInBranch,
                'ocupacion_porcentaje' => $branch->capacidad_vehiculos > 0
                    ? round(($devicesInBranch / $branch->capacidad_vehiculos) * 100, 1)
                    : 0,
            ];
        })->toArray();

        $this->dispatch('mapDataUpdated',
            vehicles: $this->vehicles,
            branches: $this->branches,
            stats: $this->stats
        );
    }

    private function calculateStats($devices)
    {
        $totalDevices = $devices->count();

        $this->stats = [
            'total_devices' => $totalDevices,
            'active_vehicles' => $devices->where('device_type', 'vehiculo')->where('status', 'active')->count(),
            'active_conductors' => $devices->where('device_type', 'conductor')->where('status', 'active')->count(),
            'charging' => $devices->where('status', 'charging')->count(),
            'maintenance_needed' => $devices->filter(fn($d) => $d->needsMaintenance())->count(),
            'avg_battery' => $totalDevices > 0 ? round($devices->avg('battery'), 1) : 0,
        ];
    }

    private function getStatusLabel($status)
    {
        return match ($status) {
            'active' => 'En ruta',
            'idle' => 'En espera',
            'charging' => 'Cargando',
            'maintenance' => 'Mantenimiento',
            'offline' => 'Desconectado',
            default => 'Desconocido',
        };
    }

    public function refreshMap()
    {
        $this->loadMapData();
        $this->dispatch('showNotification', message: 'Mapa actualizado', type: 'success');
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
        $this->dispatch($this->autoRefresh ? 'startAutoRefresh' : 'stopAutoRefresh', interval: $this->refreshInterval);
    }

    public function focusVehicle($deviceId)
    {
        $vehicle = collect($this->vehicles)->firstWhere('device_id', $deviceId);
        if ($vehicle) {
            $this->dispatch('focusOnVehicle', vehicle: $vehicle);
        }
    }

    public function loadRoute($deviceId)
    {
        $route = Telemetria::where('device_id', $deviceId)
            ->orderByDesc('id')
            ->take(15)
            ->get(['lat', 'lon'])
            ->reverse()
            ->map(fn($p) => [(float) $p->lat, (float) $p->lon])
            ->toArray();

        $this->dispatch('vehicleRouteLoaded', deviceId: $deviceId, route: $route);
    }

    public function render()
    {
        return view('livewire.map.map-view');
    }
}
