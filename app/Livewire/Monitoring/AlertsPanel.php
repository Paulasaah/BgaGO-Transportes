<?php

namespace App\Livewire\Monitoring;

use Livewire\Component;
use App\Facades\Data;

class AlertsPanel extends Component
{
    public array $alerts = [];

    protected $listeners = ['refresh-monitoring' => 'loadAlerts'];

    public function mount()
    {
        $this->loadAlerts();
    }

    public function loadAlerts()
    {
        try {
            $alertsData = Data::getActiveAlerts();
            $this->alerts = is_array($alertsData) ? $alertsData : [];
        } catch (\Exception $e) {
            \Log::error('Error loading alerts: ' . $e->getMessage());
            $this->alerts = [];
        }
    }

    public function getSeverityColor($severity): string
    {
        return match($severity) {
            'error' => 'danger',
            'warning' => 'warning',
            'info' => 'info',
            default => 'secondary'
        };
    }

    public function render()
    {
        return view('livewire.monitoring.alerts-panel');
    }
}