<?php

namespace App\Livewire\Map;

use Livewire\Component;
use App\Services\MockDataService;

class MapView extends Component
{
    public $vehicles = [];
    public $routes = [];
    public $filters = [];
    public $selectedSede = '';
    public $selectedStatus = '';
    public $autoRefresh = true;
    public $refreshInterval = 10;

    public function mount()
    {
        $this->loadFilters();
        $this->loadMapData();
    }

    public function loadFilters()
    {
        $this->filters = MockDataService::getMapFilters();
    }

    public function loadMapData()
    {
        $this->vehicles = MockDataService::getVehicleLocations();
        $this->routes = MockDataService::getActiveRoutes();
        
        if ($this->selectedSede) {
            $this->vehicles = array_filter($this->vehicles, function($v) {
                return isset($v['sede']) && $v['sede'] === $this->selectedSede;
            });
        }
        
        if ($this->selectedStatus) {
            $this->vehicles = array_filter($this->vehicles, function($v) {
                return $v['status'] === $this->selectedStatus;
            });
        }

        $this->vehicles = array_values($this->vehicles);
        
        $this->dispatch('mapDataUpdated', [
            'vehicles' => $this->vehicles,
            'routes' => $this->routes
        ]);
    }

    public function applyFilters()
    {
        $this->loadMapData();
    }

    public function clearFilters()
    {
        $this->selectedSede = '';
        $this->selectedStatus = '';
        $this->loadMapData();
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

    public function refreshMap()
    {
        $this->loadMapData();
        $this->dispatch('refreshNotification');
    }

    public function focusVehicle($vehicleId)
    {
        $vehicle = collect($this->vehicles)->firstWhere('id', $vehicleId);
        
        if ($vehicle) {
            $this->dispatch('focusOnVehicle', vehicle: $vehicle);
        }
    }

    public function render()
    {
        return view('livewire.map.map-view', [
            'vehiclesCount' => count($this->vehicles),
            'activeRoutesCount' => count($this->routes)
        ]);
    }
}