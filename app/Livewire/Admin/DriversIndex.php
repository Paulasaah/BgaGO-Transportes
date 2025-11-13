<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Traits\WithAlerts;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Vehicle;
use App\Facades\Data;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class DriversIndex extends Component
{
    use WithPagination;
    use WithAlerts;

    public string $search = '';
    public string $estado = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEstado()
    {
        $this->resetPage();
    }

    #[On('confirm-delete-ok')]
    public function onConfirmDelete()
    {
        $this->handleConfirmDelete();
        $this->resetPage();
    }

    public function toggleStatus(int $userId): void
    {
        try {
            DB::beginTransaction();

            $user = User::with('driverProfile')->findOrFail($userId);
            if (!$user->driverProfile) {
                $this->notifyError('El usuario no tiene perfil de conductor');
                DB::rollBack();
                return;
            }

            $current = (bool) $user->driverProfile->is_active;
            $user->driverProfile->update(['is_active' => !$current]);

            DB::commit();
            $this->notifySuccess(($current ? 'Conductor desactivado' : 'Conductor activado') . ' correctamente');
            $this->resetPage();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->notifyError('No se pudo actualizar el estado del conductor');
        }
    }

    public function delete(): void
    {
        $id = $this->pendingDeleteId;
        if (!$id) {
            return;
        }

        try {
            DB::beginTransaction();

            $user = User::with('driverProfile')->findOrFail($id);

            Vehicle::where('conductor_id', $user->id)->update(['conductor_id' => null]);

            if ($user->driverProfile) {
                $user->driverProfile()->delete();
            }

            if (method_exists($user, 'hasRole') && $user->hasRole('conductor')) {
                $user->removeRole('conductor');
            }

            DB::commit();
            $this->notifySuccess('Perfil de conductor eliminado');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->notifyError('No se pudo eliminar el perfil de conductor');
        }
    }

    public function render()
    {
        $stats = Data::getDriverStats();

        $drivers = User::with(['driverProfile'])
            ->where(function($q) {
                $q->whereHas('roles', fn($r) => $r->where('name', 'conductor'))
                  ->orWhereHas('driverProfile');
            })
            ->when($this->search, function($query) {
                $s = $this->search;
                $query->where(function($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%")
                      ->orWhereHas('driverProfile', function($dq) use ($s) {
                          $dq->where('license_number', 'like', "%{$s}%");
                      });
                });
            })
            ->when($this->estado, function($query) {
                if ($this->estado === 'activo') {
                    $query->whereHas('driverProfile', fn($q) => $q->where('is_active', true));
                } elseif ($this->estado === 'inactivo') {
                    $query->whereHas('driverProfile', fn($q) => $q->where('is_active', false));
                }
            })
            ->orderBy('name')
            ->paginate(15);

        foreach ($drivers as $driver) {
            $driver->assignedVehicle = Vehicle::where('conductor_id', $driver->id)->first();
        }

        return view('livewire.admin.drivers-index', compact('drivers', 'stats'));
    }
}