<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Branch;
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
     * Show the form for creating a new reservation
     */
    public function create()
    {
        $clientes = User::role('cliente')->orderBy('name')->get();
        $conductores = User::role('conductor')->orderBy('name')->get();
        $vehiculos = Vehicle::orderBy('placa')->get();
        $sedes = Branch::orderBy('nombre')->get();
        $tipos = ReservationType::cases();

        return view('admin.reservations.create', compact('clientes', 'conductores', 'vehiculos', 'sedes', 'tipos'));
    }

    /**
     * Store a newly created reservation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:reservations,codigo',
            'user_id' => 'required|exists:users,id',
            'conductor_id' => 'nullable|exists:users,id',
            'vehiculo_id' => 'nullable|exists:vehicles,id',
            'sede_id' => 'nullable|exists:branches,id',
            'tipo' => 'required|string|in:' . implode(',', array_column(ReservationType::cases(), 'value')),
            'estado' => 'nullable|string|in:' . implode(',', array_column(ReservationStatus::cases(), 'value')),
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'monto' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'monto_final' => 'nullable|numeric|min:0',
            'notas_admin' => 'nullable|string|max:1000',
        ]);

        $validated['estado'] = $validated['estado'] ?? ReservationStatus::Pendiente->value;

        $reservation = Reservation::create($validated);

        return redirect()
            ->route('admin.reservations.show', $reservation)
            ->with('success', 'Reserva creada exitosamente');
    }

    /**
     * Show the form for editing the specified reservation
     */
    public function edit(Reservation $reservation)
    {
        $clientes = User::role('cliente')->orderBy('name')->get();
        $conductores = User::role('conductor')->orderBy('name')->get();
        $vehiculos = Vehicle::orderBy('placa')->get();
        $sedes = Branch::orderBy('nombre')->get();
        $tipos = ReservationType::cases();

        $reservation->load(['user', 'vehicle', 'driver', 'branch']);

        return view('admin.reservations.edit', compact('reservation', 'clientes', 'conductores', 'vehiculos', 'sedes', 'tipos'));
    }

    /**
     * Update the specified reservation
     */
    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'conductor_id' => 'nullable|exists:users,id',
            'vehiculo_id' => 'nullable|exists:vehicles,id',
            'sede_id' => 'nullable|exists:branches,id',
            'tipo' => 'required|string|in:' . implode(',', array_column(ReservationType::cases(), 'value')),
            'estado' => 'required|string|in:' . implode(',', array_column(ReservationStatus::cases(), 'value')),
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'monto' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'monto_final' => 'nullable|numeric|min:0',
            'notas_admin' => 'nullable|string|max:1000',
        ]);

        $reservation->update($validated);

        return redirect()
            ->route('admin.reservations.show', $reservation)
            ->with('success', 'Reserva actualizada exitosamente');
    }

    /**
     * Remove the specified reservation
     */
    public function destroy(Reservation $reservation)
    {
        if (in_array($reservation->estado, [ReservationStatus::Confirmada, ReservationStatus::Activa])) {
            return back()->with('error', 'No se puede eliminar una reserva activa o confirmada');
        }

        $reservation->delete();

        return redirect()
            ->route('admin.reservations.index')
            ->with('success', 'Reserva eliminada exitosamente');
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