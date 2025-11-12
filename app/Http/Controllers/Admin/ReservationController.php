<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\InteractsWithApiController;
use App\Http\Controllers\Api\ReservationController as ApiController;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Models\Branch;
use App\Models\User;
use App\Enums\ReservationStatus;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    use InteractsWithApiController;

    /**
     * Display a listing of reservations
     */
    public function index()
    {
        return view('admin.reservations.index');
    }

    /**
     * Show form to create new reservation
     */
    public function create()
    {
        return view('admin.reservations.create', [
            'vehicles' => Vehicle::where('estado', 'disponible')->with('driver')->get(),
            'branches' => Branch::where('is_active', true)->get(),
            'users' => User::role('cliente')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store new reservation (via API)
     */
    public function store(StoreReservationRequest $request)
    {
        return $this->createViaApi(
            ApiController::class,
            $request,
            'admin.reservations.index'
        );
    }

    /**
     * Display the specified reservation
     */
    public function show(Reservation $reservation)
    {
        $reservation->load([
            'user',
            'vehicle.driver',
            'driver',
            'branch',
            'payments',
            'deliveries'
        ]);

        return view('admin.reservations.show', compact('reservation'));
    }

    /**
     * Show form to edit reservation
     */
    public function edit(Reservation $reservation)
    {
        if ($reservation->estado !== ReservationStatus::Pendiente) {
            return redirect()
                ->route('admin.reservations.show', $reservation)
                ->with('error', 'Solo se pueden editar reservas pendientes');
        }

        return view('admin.reservations.edit', [
            'reservation' => $reservation,
            'vehicles' => Vehicle::where('estado', 'disponible')
                ->orWhere('id', $reservation->vehiculo_id)
                ->get(),
            'branches' => Branch::where('is_active', true)->get(),
        ]);
    }

    /**
     * Update reservation (via API)
     */
    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        return $this->updateViaApi(
            ApiController::class,
            $request,
            $reservation,
            'admin.reservations.show'
        );
    }

    /**
     * Cancel reservation (via API or fallback)
     */
    public function cancel(Reservation $reservation, Request $request)
    {
        if (in_array($reservation->estado, [ReservationStatus::Completada, ReservationStatus::Cancelada])) {
            return back()->with('error', 'No se puede cancelar una reserva completada o ya cancelada');
        }

        return $this->delegateToApi(
            ApiController::class,
            'cancel',
            [$reservation, $request],
            null,
            'Reserva cancelada exitosamente'
        );
    }

    /**
     * Confirm reservation (via API or fallback)
     */
    public function confirm(Reservation $reservation)
    {
        if ($reservation->estado !== ReservationStatus::Pendiente) {
            return back()->with('error', 'Solo se pueden confirmar reservas pendientes');
        }

        return $this->delegateToApi(
            ApiController::class,
            'confirm',
            [$reservation, new Request()],
            null,
            'Reserva confirmada exitosamente'
        );
    }
}
