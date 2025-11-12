<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Facades\Data;

class RevenueReport extends Component
{
    public $periodo = 'mes';
    public $reporte = [];

    public function mount($periodo = 'mes')
    {
        $this->periodo = $periodo;
        $this->reporte = Data::getRevenueReport($this->periodo);
    }

    public function render()
    {
        return view('livewire.reports.revenue-report', [
            'reporte' => $this->reporte,
        ]);
    }
}
