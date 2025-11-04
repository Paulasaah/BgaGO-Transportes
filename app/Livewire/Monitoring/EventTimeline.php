<?php

namespace App\Livewire\Monitoring;

use Livewire\Component;
use App\Facades\Data;
use Livewire\Attributes\On;

class EventTimeline extends Component
{
    public $events = [];

    public function mount()
    {
        $this->loadEvents();
    }

    #[On('refresh-monitoring')]
    public function loadEvents()
    {
        $this->events = Data::getEventTimeline();
    }

    public function render()
    {
        return view('livewire.monitoring.event-timeline');
    }
}