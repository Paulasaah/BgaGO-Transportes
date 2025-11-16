<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Facades\Data;
use Livewire\Attributes\On;

class AlertsWidget extends Component
{
    public $alerts = [];
    public $alertCount = 0;

    public function mount()
    {
        $this->loadAlerts();
    }

    #[On('refreshDashboard')]
    public function loadAlerts()
    {
        $this->alerts = Data::getActiveAlerts();
        $this->alertCount = count($this->alerts);
    }

    public function dismissAlert($alertId)
    {
        // Filtrar la alerta del array
        $this->alerts = collect($this->alerts)
            ->reject(fn($alert) => $alert['id'] === $alertId)
            ->values()
            ->toArray();
        
        $this->alertCount = count($this->alerts);
        
        $this->dispatch('alert-dismissed', alertId: $alertId);
    }

    public function render()
    {
        return view('livewire.dashboard.alerts-widget');
    }
}
