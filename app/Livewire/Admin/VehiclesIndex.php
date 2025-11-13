<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use App\Livewire\Traits\WithAlerts;
use App\Models\Vehicle;
use App\Enums\VehicleStatus;
use App\Facades\Data;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class VehiclesIndex extends Component
{
    use WithPagination;
    use WithAlerts;

    public string $search = '';
    public string $estado = '';
    public string $tipo = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEstado()
    {
        $this->resetPage();
    }

    public function updatingTipo()
    {
        $this->resetPage();
    }

    #[On('confirm-delete-ok')]
    public function onConfirmDelete()
    {
        $this->handleConfirmDelete();
        $this->resetPage();
    }

    public function delete(): void
    {
        $id = $this->pendingDeleteId;
        if (!$id) {
            return;
        }

        DB::beginTransaction();
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();
        DB::commit();
    }

    public function render()
    {
        $stats = Data::getVehicleStats();

        $vehicles = Vehicle::with(['driver', 'branch'])
            ->when($this->search, function($query) {
                $s = $this->search;
                $query->where(function($q) use ($s) {
                    $q->where('placa', 'like', "%{$s}%")
                      ->orWhere('marca', 'like', "%{$s}%")
                      ->orWhere('modelo', 'like', "%{$s}%");
                });
            })
            ->when($this->estado, function($query) {
                $query->where('estado', $this->estado);
            })
            ->when($this->tipo, function($query) {
                $query->where('tipo', $this->tipo);
            })
            ->orderBy('placa')
            ->paginate(15);

        return view('livewire.admin.vehicles-index', compact('vehicles', 'stats'));
    }
}