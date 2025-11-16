<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Facades\Data;
use Livewire\Attributes\On;

class ActiveServicesWidget extends Component
{
    public $services = [];
    public $serviceCount = 0;

    public function mount()
    {
        $this->loadServices();
    }

    #[On('refreshDashboard')]
    public function loadServices()
    {
        $this->services = Data::getLiveServices();
        $this->serviceCount = count($this->services);
    }

    public function viewOnMap($serviceId)
    {
        return redirect()->route('admin.map', ['service' => $serviceId]);
    }

    public function render()
    {
        return view('livewire.dashboard.active-services-widget');
    }
}
