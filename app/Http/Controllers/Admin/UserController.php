<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\ReservationService;
use App\Models\Reservation;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage
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

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'phone' => $validated['phone'] ?? null,
        ]);

        // Asignar rol
        $user->assignRole($validated['role']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load('roles');

        $reservationService = app(ReservationService::class);
        $stats = $reservationService->getUserStats($user->id);

        $paymentsCount = Payment::where('user_id', $user->id)->count();
        $lastReservation = Reservation::with(['vehicle', 'driver', 'branch'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->first();

        $recentReservations = Reservation::with(['vehicle', 'driver', 'branch'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $recentPayments = Payment::with(['reservation'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.users.show', compact(
            'user',
            'stats',
            'paymentsCount',
            'lastReservation',
            'recentReservations',
            'recentPayments'
        ));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $user->load('roles');
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage
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

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        // Actualizar contraseña solo si se proporciona
        if (!empty($validated['password'])) {
            $user->update(['password' => bcrypt($validated['password'])]);
        }

        // Sincronizar rol
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Remove the specified user from storage
     */
    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();

            if (Auth::id() === $user->id) {
                DB::rollBack();
                return back()->with('error', 'No puedes eliminar tu propio usuario');
            }

            $user->tokens()->delete();
            DB::table('sessions')->where('user_id', $user->id)->delete();

            $user->delete();

            DB::commit();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Usuario eliminado exitosamente');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al eliminar usuario: ' . $e->getMessage());

            return back()->with('error', 'No se pudo eliminar el usuario');
        }
    }
}