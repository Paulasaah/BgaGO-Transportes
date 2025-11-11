<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Reservation\StoreReservationRequest;
use App\Http\Requests\Reservation\CancelReservationRequest;
use App\Http\Requests\Reservation\RateReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReservationController extends BaseApiController
{
    use AuthorizesRequests;

    public function __construct(
        protected ReservationService $reservationService
    ) {}

    /**
     * Listar todas las reservas (admin)
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Reservation::class);

        $perPage = $request->input('per_page', 15);
        $status = $request->input('status');
        $type   = $request->input('type');

        $query = Reservation::with(['user', 'vehicle', 'driver', 'branch', 'delivery']);

        // Filtros opcionales
        if ($status) {
            $query->where('estado', $status);
        }

        if ($type) {
            $query->where('tipo', $type);
        }

        $query->orderByDesc('created_at');
        $reservations = $query->paginate($perPage);

        return $this->success([
            'reservations' => ReservationResource::collection($reservations),
            'pagination' => [
                'total'         => $reservations->total(),
                'per_page'      => $reservations->perPage(),
                'current_page'  => $reservations->currentPage(),
                'last_page'     => $reservations->lastPage(),
            ],
        ]);
    }

    /**
     * Crear nueva reserva
     */
    public function store(StoreReservationRequest $request): JsonResponse
    {
        if (!auth()->check()) {
            return $this->error('Usuario no autenticado', 401);
        }

        $data = $request->validated();
        $data['user_id'] = auth()->id(); // ✅ Corregido

        $result = $this->reservationService->createReservation($data);

        if (!$result['success']) {
            return $this->error($result['message']);
        }

        return $this->created(
            new ReservationResource($result['data']),
            'Reserva creada exitosamente'
        );
    }

    /**
     * Ver detalle de reserva
     */
    public function show(Reservation $reservation): JsonResponse
    {
        $this->authorize('view', $reservation);

        $reservation->load([
            'user',
            'vehicle.branch',
            'driver.driverProfile',
            'branch',
            'delivery',
            'payments',
        ]);

        return $this->success(new ReservationResource($reservation));
    }

    /**
     * Confirmar reserva (después del pago)
     */
    public function confirm(Reservation $reservation): JsonResponse
    {
        $this->authorize('confirm', $reservation);

        $result = $this->reservationService->confirmReservation($reservation->id);

        return $this->handleServiceResult($result, 'Reserva confirmada exitosamente');
    }

    /**
     * Iniciar reserva
     */
    public function start(Reservation $reservation): JsonResponse
    {
        $this->authorize('start', $reservation);

        $result = $this->reservationService->startReservation($reservation->id);

        return $this->handleServiceResult($result, 'Reserva iniciada exitosamente');
    }

    /**
     * Completar reserva
     */
    public function complete(Reservation $reservation): JsonResponse
    {
        $this->authorize('complete', $reservation);

        $result = $this->reservationService->completeReservation($reservation->id);

        return $this->handleServiceResult($result, 'Reserva completada exitosamente');
    }

    /**
     * Cancelar reserva
     */
    public function cancel(CancelReservationRequest $request, Reservation $reservation): JsonResponse
    {
        $this->authorize('cancel', $reservation);

        $result = $this->reservationService->cancelReservation(
            $reservation->id,
            $request->validated()
        );

        return $this->handleServiceResult($result, 'Reserva cancelada exitosamente');
    }

    /**
     * Calificar reserva
     */
    public function rate(RateReservationRequest $request, Reservation $reservation): JsonResponse
    {
        $this->authorize('rate', $reservation);

        $reservation->update([
            'calificacion_cliente' => $request->calificacion,
            'comentario_cliente'   => $request->comentario,
        ]);

        return $this->success(
            new ReservationResource($reservation->fresh()),
            'Calificación registrada exitosamente'
        );
    }

    /**
     * Obtener reservas del usuario autenticado
     */
    public function myReservations(Request $request): JsonResponse
    {
        $user   = $request->user();
        $status = $request->input('status');
        $type   = $request->input('type', 'reserva');

        $query = Reservation::where('user_id', $user->id)
            ->where('tipo', $type)
            ->with(['vehicle', 'driver', 'branch', 'delivery', 'payments']);

        if ($status) {
            $query->where('estado', $status);
        }

        $reservations = $query->orderByDesc('created_at')->get();

        return $this->success([
            'activas' => ReservationResource::collection(
                $reservations->whereIn('estado.value', ['confirmada', 'activa'])
            ),
            'historial' => ReservationResource::collection(
                $reservations->whereIn('estado.value', ['completada', 'cancelada'])
            ),
        ]);
    }

    /**
     * Estadísticas del usuario autenticado
     */
    public function myStats(Request $request): JsonResponse
    {
        $user = $request->user();
        $result = $this->reservationService->getUserStats($user->id);

        return $this->handleServiceResult($result);
    }
}
