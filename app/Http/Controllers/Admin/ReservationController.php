<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Enums\ReservationStatus;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of reservations
     */
    public function index()
    {
        return view('admin.reservations.index');
    }

    /**
     * Display the specified reservation
     */
    public function show(Reservation $reservation)
    {
        $reservation->load(['user', 'vehicle', 'driver', 'branch']);
        return view('admin.reservations.show', compact('reservation'));
    }

    /**
     * Cancel a reservation
     */
    public function cancel(Reservation $reservation)
    {
        // Verificar que la reserva no esté ya completada o cancelada
        if (in_array($reservation->estado, [ReservationStatus::Completada, ReservationStatus::Cancelada])) {
            return back()->with('error', 'No se puede cancelar una reserva completada o ya cancelada');
        }

        $reservation->update([
            'estado' => ReservationStatus::Cancelada,
            'fecha_cancelacion' => now(),
        ]);

        // Si tenía vehículo asignado, liberarlo
        if ($reservation->vehicle) {
            $reservation->vehicle->update([
                'estado' => \App\Enums\VehicleStatus::Disponible
            ]);
        }

        return back()->with('success', 'Reserva cancelada exitosamente');
    }
}