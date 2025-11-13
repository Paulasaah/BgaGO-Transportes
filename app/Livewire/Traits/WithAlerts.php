<?php

namespace App\Livewire\Traits;

trait WithAlerts
{
    public ?int $pendingDeleteId = null;

    public function notifySuccess(string $message): void
    {
        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function notifyError(string $message): void
    {
        $this->dispatch('notify', type: 'error', message: $message);
    }

    public function notifyWarning(string $message): void
    {
        $this->dispatch('notify', type: 'warning', message: $message);
    }

    public function notifyInfo(string $message): void
    {
        $this->dispatch('notify', type: 'info', message: $message);
    }

    public function confirmDelete(int $id): void
    {
        $this->pendingDeleteId = $id;
        $this->dispatch('open-confirm', id: $id);
    }

    public function handleConfirmDelete(?int $id = null): void
    {
        $this->pendingDeleteId = $id ?? $this->pendingDeleteId;

        try {
            if (method_exists($this, 'delete')) {
                $this->delete();
                $this->notifySuccess('Eliminado correctamente.');
            } else {
                $this->notifyError('Acción de eliminación no implementada.');
            }
        } catch (\Throwable $e) {
            $this->notifyError('No se pudo eliminar: ' . $e->getMessage());
        } finally {
            $this->pendingDeleteId = null;
        }
    }
}