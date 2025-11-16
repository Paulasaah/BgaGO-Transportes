<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Facades\Data;
use Livewire\Attributes\On;

class RecentActivityWidget extends Component
{
    public $events = [];
    public $limit = 10;

    public function mount()
    {
        $this->loadEvents();
    }

    #[On('refreshDashboard')]
    public function loadEvents()
    {
        $this->events = Data::getRecentEvents($this->limit);
    }

    public function loadMore()
    {
        $this->limit += 10;
        $this->loadEvents();
    }

    public function getRelativeTime($timestamp)
    {
        $time = \Carbon\Carbon::parse($timestamp);
        return $time->diffForHumans();
    }

    public function render()
    {
        return view('livewire.dashboard.recent-activity-widget');
    }
}
