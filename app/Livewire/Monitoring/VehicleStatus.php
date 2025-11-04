<?php

namespace App\Livewire\Monitoring;

use Livewire\Component;
use App\Facades\Data;
use Livewire\Attributes\On;

class VehicleStatus extends Component
{
    public $vehicles = [];
    public $summary = [
        'activos' => 0,
        'disponibles' => 0,
        'mantenimiento' => 0,
        'fuera_servicio' => 0
    ];

    public function mount()
    {
        $this->loadVehicleStatus();
    }

    #[On('refresh-monitoring')]
    public function loadVehicleStatus()
    {
        try {
            $summaryData = Data::getVehicleStatusSummary();
            
            // Extraer los valores 'count' de la estructura anidada
            $this->summary = [
                'activos' => ($summaryData['en_servicio']['count'] ?? 0),
                'disponibles' => ($summaryData['disponibles']['count'] ?? 0),
                'mantenimiento' => ($summaryData['mantenimiento']['count'] ?? 0),
                'fuera_servicio' => 0 // No existe en los datos mock
            ];
            
            $vehicleData = Data::getVehicleLocations();
            $this->vehicles = is_array($vehicleData) ? array_slice($vehicleData, 0, 10) : [];
            
        } catch (\Exception $e) {
            \Log::error('Error loading vehicle status: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.monitoring.vehicle-status');
    }
}