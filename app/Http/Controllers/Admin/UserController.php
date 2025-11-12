<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Mostrar listado de usuarios con filtros y estadísticas.
     */
    public function index(Request $request)
    {
        // Filtro dinámico
        $query = User::with('roles')
            ->when($request->search, fn($q, $s) =>
                $q->where(fn($sub) =>
                    $sub->where('name', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%")
                )
            )
            ->when($request->role, fn($q, $r) => $q->role($r))
            ->orderByDesc('created_at');

        $users = $query->paginate(15)->withQueryString();

        // Estadísticas globales
        $allUsers = User::with('roles')->get();
        $stats = [
            'total' => $allUsers->count(),
            'clientes' => $allUsers->filter(fn($u) => $u->hasRole('cliente'))->count(),
            'conductores' => $allUsers->filter(fn($u) => $u->hasRole('conductor'))->count(),
            'admins' => $allUsers->filter(fn($u) => $u->hasRole('admin'))->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Guardar nuevo usuario.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,conductor,cliente',
        ]);

        $validated['role'] = Str::lower($validated['role']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'phone' => $validated['phone'] ?? null,
        ]);

        $user->assignRole($validated['role']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Mostrar detalles del usuario.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'reservations.vehicle', 'payments.reservation']);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(User $user)
    {
        $user->load('roles');
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Actualizar datos del usuario.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,conductor,cliente',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $validated['role'] = Str::lower($validated['role']);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => bcrypt($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Eliminar usuario.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        if ($user->reservations()->whereNotIn('estado', ['cancelada', 'completada'])->exists()) {
            return back()->with('error', 'No puedes eliminar un usuario con reservas activas.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}
