<?php

namespace App\Livewire\Monitoring;

use Livewire\Component;
use App\Facades\Data;

class EventTimeline extends Component
{
    public array $events = [];

    protected $listeners = ['refresh-monitoring' => 'loadEvents'];

    public function mount()
    {
        $this->loadEvents();
    }

    public function loadEvents()
    {
        try {
            $eventsData = Data::getRecentEvents(10);
            $this->events = is_array($eventsData) ? $eventsData : [];
        } catch (\Exception $e) {
            \Log::error('Error loading events: ' . $e->getMessage());
            $this->events = [];
        }
    }

    public function render()
    {
        return view('livewire.monitoring.event-timeline');
    }
}