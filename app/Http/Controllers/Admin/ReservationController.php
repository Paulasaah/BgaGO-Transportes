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

    public function create()
    {
        $users = \App\Models\User::orderBy('name')->get();
        $vehicles = \App\Models\Vehicle::where('estado', 'disponible')->orderBy('placa')->get();
        $conductores = \App\Models\User::role('conductor')->orderBy('name')->get();
        $sedes = \App\Models\Branch::orderBy('nombre')->get();
        
        return view('admin.reservations.create', compact('users', 'vehicles', 'conductores', 'sedes'));
    }

    public function store(Request $request)
    {
        // Validaciones base
        $rules = [
            'user_id' => 'required|exists:users,id',
            'tipo' => 'required|in:reserva,domicilio',
            'fecha_inicio' => 'required|date',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'conductor_id' => 'nullable|exists:users,id',
            'sede_id' => 'nullable|exists:branches,id',
            'observaciones' => 'nullable|string|max:1000',
        ];

        // Validaciones según tipo de servicio
        if ($request->tipo === 'reserva') {
            // Para reservas: fecha_fin es obligatoria
            $rules['fecha_fin'] = 'required|date|after:fecha_inicio';
        } else {
            // Para domicilios: direcciones son obligatorias
            $rules['direccion_origen'] = 'required|string|max:500';
            $rules['direccion_destino'] = 'required|string|max:500';
            $rules['fecha_fin'] = 'nullable|date|after:fecha_inicio';
        }

        $validated = $request->validate($rules);

        // Generar código único
        $validated['codigo'] = 'RES-' . strtoupper(uniqid());
        $validated['estado'] = ReservationStatus::Pendiente;

        // Si es domicilio y no tiene fecha_fin, calcular automáticamente (+2 horas)
        if ($request->tipo === 'domicilio' && !isset($validated['fecha_fin'])) {
            $validated['fecha_fin'] = \Carbon\Carbon::parse($validated['fecha_inicio'])->addHours(2);
        }

        $reservation = Reservation::create($validated);

        return redirect()
            ->route('admin.reservations.show', $reservation)
            ->with('notification', [
                'type' => 'success',
                'message' => 'Reserva creada exitosamente'
            ]);
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
            return back()->with('notification', [
                'type' => 'error',
                'message' => 'No se puede cancelar una reserva completada o ya cancelada'
            ]);
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

        return back()->with('notification', [
            'type' => 'success',
            'message' => 'Reserva cancelada exitosamente'
        ]);
    }
}