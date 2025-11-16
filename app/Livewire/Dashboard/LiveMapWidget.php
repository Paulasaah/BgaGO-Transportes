<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Facades\Data;
use App\Models\Branch;
use Livewire\Attributes\On;

class LiveMapWidget extends Component
{
    public $vehicles = [];
    public $branches = [];
    public $stats = [
        'total' => 0,
        'active' => 0,
        'available' => 0,
        'maintenance' => 0,
    ];

    public function mount()
    {
        $this->loadMapData();
    }

    #[On('refreshDashboard')]
    public function loadMapData()
    {
        // Cargar ubicaciones de vehículos
        $this->vehicles = Data::getVehicleLocations();
        
        // Cargar sedes
        $this->branches = Branch::select('id', 'nombre', 'lat', 'lon', 'radio')
            ->get()
            ->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'nombre' => $branch->nombre,
                    'lat' => $branch->lat,
                    'lng' => $branch->lon,
                    'radio' => $branch->radio ?? 5000,
                ];
            })
            ->toArray();

        // Calcular estadísticas
        $vehicleCollection = collect($this->vehicles);
        $this->stats = [
            'total' => $vehicleCollection->count(),
            'active' => $vehicleCollection->where('status', 'busy')->count(),
            'available' => $vehicleCollection->where('status', 'available')->count(),
            'maintenance' => $vehicleCollection->where('status', 'maintenance')->count(),
        ];
    }

    public function viewFullMap()
    {
        return redirect()->route('admin.map');
    }

    public function render()
    {
        return view('livewire.dashboard.live-map-widget');
    }
}
