<?php

namespace App\Livewire\Monitoring;

use Livewire\Component;
use App\Facades\Data;

class VehicleStatus extends Component
{
    public array $vehicles = [];
    public array $summary = [
        'activos' => 0,
        'disponibles' => 0,
        'mantenimiento' => 0
    ];

    protected $listeners = ['refresh-monitoring' => 'loadVehicleStatus'];

    public function mount()
    {
        $this->loadVehicleStatus();
    }

    public function loadVehicleStatus()
    {
        try {
            // Obtener resumen de estado de vehículos
            $summaryData = Data::getVehicleStatusSummary();
            
            $this->summary = [
                'activos' => ($summaryData['en_servicio']['count'] ?? 0),
                'disponibles' => ($summaryData['disponibles']['count'] ?? 0),
                'mantenimiento' => ($summaryData['mantenimiento']['count'] ?? 0)
            ];
            
            // Obtener ubicaciones de vehículos
            $vehicleData = Data::getVehicleLocations();
            $this->vehicles = is_array($vehicleData) ? array_slice($vehicleData, 0, 10) : [];
            
        } catch (\Exception $e) {
            \Log::error('Error loading vehicle status: ' . $e->getMessage());
            $this->summary = [
                'activos' => 0,
                'disponibles' => 0,
                'mantenimiento' => 0
            ];
            $this->vehicles = [];
        }
    }

    public function render()
    {
        return view('livewire.monitoring.vehicle-status');
    }
}