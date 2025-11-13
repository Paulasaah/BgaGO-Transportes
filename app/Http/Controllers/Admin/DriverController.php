<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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
     * Toggle driver active status
     */
    public function toggleStatus(User $user)
    {
        // Verificar que el usuario es conductor
        if (!$user->hasRole('conductor')) {
            return back()->with('error', 'El usuario no es un conductor');
        }

        // Si no tiene profile, crear uno
        if (!$user->driverProfile) {
            $user->driverProfile()->create([
                'is_active' => true,
                'license_number' => null,
            ]);
            
            return back()->with('success', 'Perfil de conductor creado y activado');
        }

        // Toggle del estado
        $newStatus = !$user->driverProfile->is_active;
        $user->driverProfile->update(['is_active' => $newStatus]);

        $message = $newStatus 
            ? 'Conductor activado exitosamente' 
            : 'Conductor desactivado exitosamente';

        return back()->with('success', $message);
    }
}