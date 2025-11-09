<?php

namespace App\Livewire\Map;

use Livewire\Component;
use App\Models\Telemetria;
use App\Models\Branch;

class MapView extends Component
{
    public $vehicles = [];
    public $branches = [];
    public $autoRefresh = true;
    public $refreshInterval = 3;
    
    // Filtros
    public $filterType = 'all';
    public $filterStatus = 'all';
    public $showAlerts = true;
    
    // Estadísticas
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
        // Obtener últimos registros por dispositivo
        $query = Telemetria::latestByDevice();
        
        // Aplicar filtros
        if ($this->filterType !== 'all') {
            $query->where('device_type', $this->filterType);
        }
        
        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }
        
        $latestData = $query->get();
        
        // Calcular estadísticas
        $this->calculateStats($latestData);
        
        // Preparar vehículos con rutas
        $this->vehicles = $latestData->map(function ($latest) {
            $deviceId = $latest->device_id;
            
            // Historial para ruta (últimos 15 puntos)
            $route = Telemetria::where('device_id', $deviceId)
                ->orderByDesc('id')
                ->take(15)
                ->get(['lat', 'lon'])
                ->reverse()
                ->values()
                ->map(fn($p) => [(float) $p->lat, (float) $p->lon])
                ->filter(fn($point) => !empty($point[0]) && !empty($point[1]))
                ->values()
                ->toArray();
            
            return [
                'device_id' => $deviceId,
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
                'trip_count' => $latest->trip_count,
                'maintenance_km_left' => (float) $latest->maintenance_km_left,
                'needs_maintenance' => $latest->needsMaintenance(),
                'driver_name' => $latest->driver_name,
                'deliveries_completed' => $latest->deliveries_completed,
                'rating' => (float) ($latest->rating ?? 0),
                'has_route' => count($route) > 1,
                'route' => $route,
                'updated_at' => $latest->updated_at->diffForHumans(),
            ];
        })->values()->toArray();
        
        // Cargar sedes con sus datos completos
        $this->branches = Branch::all()
            ->map(function($branch) {
                // Contar dispositivos en esta sede
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
            })
            ->toArray();
        
        // Emitir actualización
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
            'active_vehicles' => $devices->where('device_type', 'vehiculo')
                                         ->where('status', 'active')
                                         ->count(),
            'active_conductors' => $devices->where('device_type', 'conductor')
                                           ->where('status', 'active')
                                           ->count(),
            'charging' => $devices->where('status', 'charging')->count(),
            'maintenance_needed' => $devices->filter(fn($d) => $d->needsMaintenance())->count(),
            'avg_battery' => $totalDevices > 0 ? round($devices->avg('battery'), 1) : 0,
        ];
    }
    
    private function getStatusLabel($status)
    {
        return match($status) {
            'active' => 'En ruta',
            'idle' => 'En espera',
            'charging' => 'Cargando',
            'maintenance' => 'Mantenimiento',
            'offline' => 'Desconectado',
            default => 'Desconocido'
        };
    }

    public function refreshMap()
    {
        $this->loadMapData();
        $this->dispatch('showNotification', 
            message: 'Mapa actualizado',
            type: 'success'
        );
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
        
        if ($this->autoRefresh) {
            $this->dispatch('startAutoRefresh', interval: $this->refreshInterval);
        } else {
            $this->dispatch('stopAutoRefresh');
        }
    }

    public function focusVehicle($deviceId)
    {
        $vehicle = collect($this->vehicles)->firstWhere('device_id', $deviceId);
        
        if ($vehicle) {
            $this->dispatch('focusOnVehicle', vehicle: $vehicle);
        }
    }
    
    public function updatedFilterType()
    {
        $this->loadMapData();
    }
    
    public function updatedFilterStatus()
    {
        $this->loadMapData();
    }

    public function render()
    {
        return view('livewire.map.map-view');
    }
}