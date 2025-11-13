<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use App\Livewire\Traits\WithAlerts;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Enums\ReservationStatus;
use App\Facades\Data;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class ReservationsIndex extends Component
{
    use WithPagination;
    use WithAlerts;

    public string $search = '';
    public string $tipo = '';
    public string $estado = '';
    public string $fecha = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingTipo() { $this->resetPage(); }
    public function updatingEstado() { $this->resetPage(); }
    public function updatingFecha() { $this->resetPage(); }

    #[On('confirm-delete-ok')]
    public function onConfirmDelete(): void
    {
        $this->handleConfirmDelete();
        $this->resetPage();
    }

    public function handleConfirmDelete(?int $id = null): void
    {
        $this->pendingDeleteId = $id ?? $this->pendingDeleteId;
        try {
            $this->delete();
            $this->notifySuccess('Reserva cancelada exitosamente');
        } catch (\Throwable $e) {
            $this->notifyError('No se pudo cancelar la reserva');
        } finally {
            $this->pendingDeleteId = null;
        }
    }

    public function delete(): void
    {
        $id = $this->pendingDeleteId;
        if (!$id) return;

        $reservation = Reservation::with('vehicle')->findOrFail($id);

        if (in_array($reservation->estado, [ReservationStatus::Completada, ReservationStatus::Cancelada])) {
            throw new \RuntimeException('No se puede cancelar una reserva completada o ya cancelada');
        }

        DB::beginTransaction();
        try {
            $reservation->update([
                'estado' => ReservationStatus::Cancelada,
                'fecha_cancelacion' => now(),
            ]);

            if ($reservation->vehicle) {
                $reservation->vehicle->update([
                    'estado' => \App\Enums\VehicleStatus::Disponible,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->tipo = '';
        $this->estado = '';
        $this->fecha = '';
        $this->resetPage();
    }

    public function render()
    {
        $stats = Data::getReservationStats();

        $reservations = Reservation::with(['user', 'vehicle', 'driver'])
            ->when($this->search, function($query) {
                $s = $this->search;
                $query->where('codigo', 'like', "%{$s}%")
                      ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$s}%"));
            })
            ->when($this->tipo, function($query) {
                $query->where('tipo', $this->tipo);
            })
            ->when($this->estado, function($query) {
                $query->where('estado', $this->estado);
            })
            ->when($this->fecha, function($query) {
                $query->whereDate('fecha_inicio', $this->fecha);
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.admin.reservations-index', compact('reservations', 'stats'));
    }
}