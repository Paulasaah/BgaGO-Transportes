<?php

namespace App\Livewire\Monitoring;

use Livewire\Component;
use App\Facades\Data;
use Livewire\Attributes\On;

class AlertsPanel extends Component
{
    public $alerts = [];

    public function mount()
    {
        $this->loadAlerts();
    }

    #[On('refresh-monitoring')]
    public function loadAlerts()
    {
        $this->alerts = Data::getAlerts();
    }

    public function getSeverityColor($severity)
    {
        return Data::getSeverityColor($severity);
    }

    public function render()
    {
        return view('livewire.monitoring.alerts-panel');
    }
}