<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Livewire\Traits\WithAlerts;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class UsersIndex extends Component
{
    use WithPagination;
    use WithAlerts;

    public string $search = '';
    public string $role = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRole()
    {
        $this->resetPage();
    }

    #[On('confirm-delete-ok')]
    public function onConfirmDelete()
    {
        $this->handleConfirmDelete();
        $this->resetPage();
    }

    public function delete()
    {
        $id = $this->pendingDeleteId;
        if (!$id) return;

        try {
            DB::beginTransaction();

            if (Auth::id() === $id) {
                DB::rollBack();
                $this->notifyError('No puedes eliminar tu propio usuario');
                return;
            }

            $user = User::findOrFail($id);
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }
            DB::table('sessions')->where('user_id', $user->id)->delete();

            $user->delete();

            DB::commit();
            $this->notifySuccess('Usuario eliminado exitosamente');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al eliminar usuario: ' . $e->getMessage());
            $this->notifyError('No se pudo eliminar el usuario');
        }
    }

    public function deleteNow(int $id)
    {
        $this->pendingDeleteId = $id;
        $this->delete();
        $this->resetPage();
    }

    public function render()
    {
        $allUsers = User::with('roles')->get();
        $stats = [
            'total' => $allUsers->count(),
            'clientes' => $allUsers->filter(fn($u) => $u->hasRole('cliente'))->count(),
            'conductores' => $allUsers->filter(fn($u) => $u->hasRole('conductor'))->count(),
            'admins' => $allUsers->filter(fn($u) => $u->hasRole('admin'))->count(),
        ];

        $users = User::with('roles')
            ->when($this->search, function($query){
                $s = $this->search;
                $query->where(function($q) use ($s){
                    $q->where('name', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%");
                });
            })
            ->when($this->role, function($query){
                $query->role($this->role);
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.admin.users-index', compact('users', 'stats'));
    }
}
