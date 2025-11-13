<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    /**
     * Display a listing of drivers
     */
    public function index()
    {
        return view('admin.drivers.index');
    }

    /**
     * Mostrar perfil del conductor
     */
    public function show(User $user)
    {
        $user->load('driverProfile');

        $assignedVehicle = \App\Models\Vehicle::where('conductor_id', $user->id)->first();

        $recentReservations = \App\Models\Reservation::with(['user', 'vehicle'])
            ->where('conductor_id', $user->id)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $stats = [
            'total' => \App\Models\Reservation::where('conductor_id', $user->id)->count(),
            'completadas' => \App\Models\Reservation::where('conductor_id', $user->id)
                ->completadas()->count(),
            'canceladas' => \App\Models\Reservation::where('conductor_id', $user->id)
                ->canceladas()->count(),
            'activas' => \App\Models\Reservation::where('conductor_id', $user->id)
                ->activas()->count(),
        ];

        return view('admin.drivers.show', compact('user', 'assignedVehicle', 'recentReservations', 'stats'));
    }

    /**
     * Mostrar formulario de edición del conductor
     */
    public function edit(User $user)
    {
        $vehicles = \App\Models\Vehicle::orderBy('placa')->get();
        $user->load('driverProfile');

        return view('admin.drivers.edit', compact('user', 'vehicles'));
    }

    /**
     * Actualizar datos del conductor
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8|confirmed',
            'license_number' => 'nullable|string|max:50',
            'license_expiry' => 'nullable|date',
            'is_active' => 'required|boolean',
            'assigned_vehicle_id' => 'nullable|exists:vehicles,id',
        ]);
        try {
            \DB::beginTransaction();

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'] ?? null;
            if (!empty($validated['password'])) {
                $user->password = bcrypt($validated['password']);
            }
            $user->save();

            $user->driverProfile()->updateOrCreate([], [
                'license_number' => $validated['license_number'] ?? null,
                'license_expiry' => $validated['license_expiry'] ?? null,
                'is_active' => (bool) $validated['is_active'],
            ]);

            if (isset($validated['assigned_vehicle_id'])) {
                $vehicle = \App\Models\Vehicle::find($validated['assigned_vehicle_id']);
                if ($vehicle) {
                    \App\Models\Vehicle::where('conductor_id', $user->id)
                        ->where('id', '!=', $vehicle->id)
                        ->update(['conductor_id' => null]);

                    $vehicle->update(['conductor_id' => $user->id]);
                }
            }

            \DB::commit();

            return redirect()
                ->route('admin.drivers.show', $user)
                ->with('success', 'Conductor actualizado exitosamente');
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Error al actualizar conductor: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'No se pudo actualizar el conductor');
        }
    }
    /**
     * Toggle driver active status
     */
    public function toggleStatus(User $user)
    {
        try {
            \DB::beginTransaction();

            if (!$user->driverProfile) {
                $user->driverProfile()->create([
                    'is_active' => true,
                    'license_number' => null,
                ]);

                \DB::commit();
                return back()->with('success', 'Perfil de conductor creado y activado');
            }

            $newStatus = !$user->driverProfile->is_active;
            $user->driverProfile->update(['is_active' => $newStatus]);

            if ($newStatus) {
                if (!$user->hasRole('conductor')) {
                    $user->assignRole('conductor');
                }
            } else {
                if ($user->hasRole('conductor')) {
                    $user->removeRole('conductor');
                }
                $user->tokens()->delete();
                DB::table('sessions')->where('user_id', $user->id)->delete();
            }

            \DB::commit();

            $message = $newStatus 
                ? 'Conductor activado exitosamente' 
                : 'Conductor desactivado exitosamente';

            return back()->with('success', $message);
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Error al cambiar estado de conductor: ' . $e->getMessage());
            return back()->with('error', 'No se pudo completar la operación');
        }
    }
}