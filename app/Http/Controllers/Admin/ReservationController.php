<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
use App\Services\ReservationService;
use App\Services\DeliveryService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationService $reservationService,
        protected DeliveryService $deliveryService,
    ) {}
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
        $vehicles = \App\Models\Vehicle::where('estado', 'disponible')
            ->where('visible_catalogo', true)
            ->orderBy('placa')
            ->get();
        $conductores = \App\Models\User::role('conductor')->orderBy('name')->get();
        $sedes = \App\Models\Branch::orderBy('nombre')->get();
        
        return view('admin.reservations.create', compact('users', 'vehicles', 'conductores', 'sedes'));
    }

    public function store(Request $request)
    {
        // Auto-asignar sede para domicilios si no se seleccionó explícitamente pero hay coordenadas de origen
        if (
            $request->tipo === 'domicilio' &&
            !$request->filled('sede_id') &&
            $request->filled('lat_origen') &&
            $request->filled('lon_origen')
        ) {
            $nearestBranch = \App\Models\Branch::findNearestTo(
                (float) $request->input('lat_origen'),
                (float) $request->input('lon_origen')
            );

            if ($nearestBranch) {
                $request->merge(['sede_id' => $nearestBranch->id]);
            }
        }

        // Validaciones base comunes
        $rules = [
            'user_id'       => 'required|exists:users,id',
            'tipo'          => 'required|in:reserva,domicilio',
            'fecha_inicio'  => 'nullable|date',
            'vehicle_id'    => 'nullable|exists:vehicles,id',
            'conductor_id'  => 'nullable|exists:users,id',
            'sede_id'       => 'nullable|exists:branches,id',
            'observaciones' => 'nullable|string|max:1000',
            // Coordenadas opcionales (por autocompletado)
            'lat_origen'    => 'nullable|numeric|between:-90,90',
            'lon_origen'    => 'nullable|numeric|between:-180,180',
            'lat_destino'   => 'nullable|numeric|between:-90,90',
            'lon_destino'   => 'nullable|numeric|between:-180,180',
        ];

        // Validaciones según tipo de servicio
        if ($request->tipo === 'reserva') {
            $rules['vehicle_id']       = 'required|exists:vehicles,id';
            $rules['fecha_fin']        = 'required|date|after:fecha_inicio';
            $rules['direccion_origen'] = 'nullable|string|max:500';
            $rules['direccion_destino']= 'nullable|string|max:500';
        } else {
            $rules['fecha_fin']        = 'nullable';
            $rules['direccion_origen'] = 'required|string|max:500';
            $rules['direccion_destino']= 'required|string|max:500';
            $rules['sede_id']          = 'required|exists:branches,id';
        }

        $validated = $request->validate($rules);

        if ($request->tipo === 'reserva') {
            $vehicle = \App\Models\Vehicle::findOrFail((int) $validated['vehicle_id']);

            $sedeId = $vehicle->sede_id ?? ($validated['sede_id'] ?? null);

            if (!$sedeId) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('notification', [
                        'type'    => 'error',
                        'message' => 'El vehículo seleccionado no tiene una sede asociada. Configura la sede del vehículo antes de crear la reserva.',
                    ]);
            }

            // Usar ReservationService para respetar la lógica de negocio (precio, disponibilidad, etc.)
            $data = [
                'user_id'           => (int) $validated['user_id'],
                'vehiculo_id'       => (int) $validated['vehicle_id'],
                'sede_id'           => (int) $sedeId,
                'fecha_inicio'      => $validated['fecha_inicio'],
                'fecha_fin'         => $validated['fecha_fin'],
                'origen_direccion'  => $validated['direccion_origen']  ?? 'Sin dirección',
                'destino_direccion' => $validated['direccion_destino'] ?? 'Sin destino',
                'notas_cliente'     => $validated['observaciones'] ?? null,
            ];

            $result = $this->reservationService->createReservation($data);

            if (!$result['success']) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('notification', [
                        'type'    => 'error',
                        'message' => $result['message'],
                    ]);
            }

            $reservation = $result['data'];
        } else {
            $deliveryData = [
                'user_id' => (int) $validated['user_id'],
                'sede_id' => (int) $validated['sede_id'],
                'direccion_origen' => $validated['direccion_origen'],
                'direccion_destino' => $validated['direccion_destino'],
                'instrucciones_especiales' => $validated['observaciones'] ?? null,
            ];

            // Solo programar domicilio si se marcó explícitamente
            if ($request->boolean('programar_domicilio') && !empty($validated['fecha_inicio'])) {
                $deliveryData['fecha_recogida'] = $validated['fecha_inicio'];
            }

            // Si el autocompletado proporcionó coordenadas, usarlas en vez de volver a geocodificar
            if (!empty($validated['lat_origen']) && !empty($validated['lon_origen'])) {
                $deliveryData['lat_origen'] = (float) $validated['lat_origen'];
                $deliveryData['lon_origen'] = (float) $validated['lon_origen'];
            }

            if (!empty($validated['lat_destino']) && !empty($validated['lon_destino'])) {
                $deliveryData['lat_destino'] = (float) $validated['lat_destino'];
                $deliveryData['lon_destino'] = (float) $validated['lon_destino'];
            }

            $result = $this->deliveryService->createPackageDelivery($deliveryData);

            if (!$result['success']) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('notification', [
                        'type'    => 'error',
                        'message' => $result['message'],
                    ]);
            }

            $delivery = $result['data'];
            $reservation = $delivery->reservation;
        }

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

    public function start(Reservation $reservation)
    {
        if (in_array($reservation->estado, [ReservationStatus::Completada, ReservationStatus::Cancelada])) {
            return back()->with('notification', [
                'type' => 'error',
                'message' => 'No se puede activar una reserva completada o cancelada',
            ]);
        }

        try {
            if ($reservation->tipo === ReservationType::Domicilio) {
                $delivery = \App\Models\Delivery::where('reserva_id', $reservation->id)->first();

                if (!$delivery) {
                    return back()->with('notification', [
                        'type' => 'error',
                        'message' => 'No se encontró el domicilio asociado a esta reserva',
                    ]);
                }

                $result = $this->deliveryService->startDelivery($delivery->id);
            } else {
                if ($reservation->estado === ReservationStatus::Pendiente) {
                    $confirmResult = $this->reservationService->confirmReservation($reservation->id);

                    if (!$confirmResult['success']) {
                        return back()->with('notification', [
                            'type' => 'error',
                            'message' => $confirmResult['message'],
                        ]);
                    }

                    $reservation = $confirmResult['data'];
                }

                $result = $this->reservationService->startReservation($reservation->id);
            }

            if (!$result['success']) {
                return back()->with('notification', [
                    'type' => 'error',
                    'message' => $result['message'],
                ]);
            }

            return back()->with('notification', [
                'type' => 'success',
                'message' => 'Reserva activada exitosamente',
            ]);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('notification', [
                'type' => 'error',
                'message' => 'Ocurrió un error al activar la reserva',
            ]);
        }
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