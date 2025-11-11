<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Delivery\StorePackageDeliveryRequest;
use App\Http\Requests\Delivery\StoreVehicleDeliveryRequest;
use App\Http\Requests\Delivery\CompleteDeliveryRequest;
use App\Http\Resources\DeliveryResource;
use App\Http\Resources\ReservationResource;
use App\Models\Delivery;
use App\Models\Reservation;
use App\Services\DeliveryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DeliveryController extends BaseApiController
{
    use AuthorizesRequests;
    public function __construct(
        protected DeliveryService $deliveryService
    ) {}

    /**
     * Listar todos los domicilios (admin)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $status = $request->input('status');
        $type = $request->input('type');

        $query = Delivery::with(['user', 'reservation.driver', 'reservation.vehicle']);

        // Filtros
        if ($type) {
            $query->where('tipo', $type);
        }

        if ($status) {
            $query->whereHas('reservation', function($q) use ($status) {
                $q->where('estado', $status);
            });
        }

        $deliveries = $query->orderByDesc('created_at')->paginate($perPage);

        return $this->success([
            'deliveries' => DeliveryResource::collection($deliveries),
            'pagination' => [
                'total' => $deliveries->total(),
                'per_page' => $deliveries->perPage(),
                'current_page' => $deliveries->currentPage(),
                'last_page' => $deliveries->lastPage(),
            ],
        ]);
    }

    /**
     * Crear domicilio de paquete
     */
    public function storePackage(StorePackageDeliveryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        
        $result = $this->deliveryService->createPackageDelivery($data);

        if (!$result['success']) {
            return $this->error($result['message']);
        }

        return $this->created(
            new DeliveryResource($result['data']),
            'Domicilio de paquete creado exitosamente'
        );
    }

    /**
     * Crear domicilio de vehículo
     */
    public function storeVehicle(StoreVehicleDeliveryRequest $request): JsonResponse
    {
        $result = $this->deliveryService->createVehicleDelivery($request->validated());

        if (!$result['success']) {
            return $this->error($result['message']);
        }

        return $this->created(
            new DeliveryResource($result['data']),
            'Domicilio de vehículo creado exitosamente'
        );
    }

    /**
     * Ver detalle de domicilio
     */
    public function show(Delivery $delivery): JsonResponse
    {
        $this->authorize('view', $delivery);

        $delivery->load([
            'user',
            'reservation.driver.driverProfile',
            'reservation.vehicle',
            'reservation.payments',
        ]);

        return $this->success(new DeliveryResource($delivery));
    }

    /**
     * Asignar conductor a domicilio
     */
    public function assignDriver(Request $request, Delivery $delivery): JsonResponse
    {
        $this->authorize('assignDriver', $delivery);

        $request->validate([
            'conductor_id' => 'required|exists:users,id',
        ]);

        $result = $this->deliveryService->assignDriver(
            $delivery->id,
            $request->conductor_id
        );

        return $this->handleServiceResult($result, 'Conductor asignado exitosamente');
    }

    /**
     * Iniciar domicilio (conductor recoge)
     */
    public function start(Delivery $delivery): JsonResponse
    {
        $this->authorize('start', $delivery); 

        $result = $this->deliveryService->startDelivery($delivery->id);

        return $this->handleServiceResult($result, 'Domicilio iniciado exitosamente');
    }

    /**
     * Completar domicilio (entrega)
     */
    public function complete(CompleteDeliveryRequest $request, Delivery $delivery): JsonResponse
    {
        $this->authorize('complete', $delivery); 

        $result = $this->deliveryService->completeDelivery(
            $delivery->id,
            $request->validated()
        );

        return $this->handleServiceResult($result, 'Domicilio completado exitosamente');
    }

    /**
     * Tracking del domicilio
     */
    public function track(Delivery $delivery): JsonResponse
    {
        $result = $this->deliveryService->trackDelivery($delivery->id);

        return $this->handleServiceResult($result);
    }

    /**
     * Mis domicilios (usuario autenticado)
     */
    public function myDeliveries(Request $request): JsonResponse
    {
        $user = $request->user();
        $status = $request->input('status');

        $query = Delivery::where('user_id', $user->id)
            ->with(['reservation.driver', 'reservation.vehicle', 'reservation.payments']);

        if ($status) {
            $query->whereHas('reservation', function($q) use ($status) {
                $q->where('estado', $status);
            });
        }

        $deliveries = $query->orderByDesc('created_at')->get();

        return $this->success([
            'activos' => DeliveryResource::collection(
                $deliveries->filter(fn($d) => in_array($d->reservation->estado->value, ['confirmada', 'activa']))
            ),
            'historial' => DeliveryResource::collection(
                $deliveries->filter(fn($d) => in_array($d->reservation->estado->value, ['completada', 'cancelada']))
            ),
        ]);
    }

    /**
     * Domicilios pendientes de asignación (admin/dispatcher)
     */
    public function pending(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Delivery::class);

        $result = $this->deliveryService->getPendingDeliveries();

        if (!$result['success']) {
            return $this->error($result['message']);
        }

        return $this->success(
            DeliveryResource::collection($result['data']),
            'Domicilios pendientes obtenidos'
        );
    }

    /**
     * Domicilios asignados al conductor autenticado
     */
    public function myAssignedDeliveries(Request $request): JsonResponse
    {
        $driver = $request->user();
        
        $deliveries = Delivery::whereHas('reservation', function($q) use ($driver) {
            $q->where('conductor_id', $driver->id)
              ->whereIn('estado', ['confirmada', 'activa']);
        })
        ->with(['user', 'reservation.vehicle'])
        ->orderByDesc('created_at')
        ->get();

        return $this->success(
            DeliveryResource::collection($deliveries),
            'Domicilios asignados obtenidos'
        );
    }
}