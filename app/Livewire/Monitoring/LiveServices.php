<?php

namespace App\Livewire\Monitoring;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Facades\Data;

class LiveServices extends Component
{
    public $services = [];
    public $selectedPriority = '';
    public $searchTerm = '';

    public function mount()
    {
        $this->loadServices();
    }

    #[On('refresh-monitoring')]
    public function loadServices()
    {
        $services = Data::getLiveServices();

        if ($this->selectedPriority) {
            $services = array_filter($services, fn($s) => $s['prioridad'] === $this->selectedPriority);
        }

        if ($this->searchTerm) {
            $search = strtolower($this->searchTerm);
            $services = array_filter($services, function($s) use ($search) {
                return str_contains(strtolower($s['codigo']), $search) ||
                       str_contains(strtolower($s['usuario']), $search) ||
                       str_contains(strtolower($s['conductor'] ?? ''), $search);
            });
        }

        $this->services = array_values($services);
    }

    public function clearFilters()
    {
        $this->selectedPriority = '';
        $this->searchTerm = '';
        $this->loadServices();
    }

    public function viewServiceDetails($serviceId)
    {
        $this->dispatch('show-service-details', serviceId: $serviceId);
    }

    public function render()
    {
        return view('livewire.monitoring.live-services');
    }
}