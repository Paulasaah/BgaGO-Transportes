<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Facades\Data;

class VehicleUsageReport extends Component
{
    public $vehiculos = [];

    public function mount()
    {
        $this->vehiculos = Data::getVehicleUsageReport();
    }

    public function render()
    {
        return view('livewire.reports.vehicle-usage-report', [
            'vehiculos' => $this->vehiculos,
        ]);
    }
}
