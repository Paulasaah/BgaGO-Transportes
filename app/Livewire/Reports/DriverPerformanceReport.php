<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Facades\Data;

class DriverPerformanceReport extends Component
{
    public $conductores = [];

    public function mount()
    {
        $this->conductores = Data::getDriverPerformance();
    }

    public function render()
    {
        return view('livewire.reports.driver-performance-report', [
            'conductores' => $this->conductores,
        ]);
    }
}
