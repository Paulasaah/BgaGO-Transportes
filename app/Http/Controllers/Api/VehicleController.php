<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use App\Services\VehicleAvailabilityService;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VehicleController extends BaseApiController
{
    public function __construct(
        protected VehicleAvailabilityService $availabilityService,
        protected PricingService $pricingService
    ) {}

    /**
     * Catálogo de vehículos disponibles
     */
    public function index(Request $request): JsonResponse
    {
        $tipo = $request->input('tipo');
        $sedeId = $request->input('sede_id');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $query = Vehicle::disponibles()
            ->with(['branch', 'driver']);

        // Filtro por tipo
        if ($tipo) {
            $query->porTipo($tipo);
        }

        // Filtro por sede
        if ($sedeId) {
            $query->porSede($sedeId);
        }

        $vehicles = $query->get();

        // Si hay fechas, filtrar por disponibilidad
        if ($fechaInicio && $fechaFin) {
            $vehicles = $vehicles->filter(function($vehicle) use ($fechaInicio, $fechaFin) {
                return $vehicle->isAvailableInRange($fechaInicio, $fechaFin);
            });
        }

        return $this->success([
            'vehicles' => VehicleResource::collection($vehicles),
            'total' => $vehicles->count(),
        ]);
    }

    /**
     * Detalle de vehículo
     */
    public function show(Vehicle $vehicle): JsonResponse
    {
        $vehicle->load(['branch', 'driver.driverProfile']);

        return $this->success(
            new VehicleResource($vehicle->loadCount('reservations'))
        );
    }

    /**
     * Verificar disponibilidad de vehículo
     */
    public function checkAvailability(Request $request, Vehicle $vehicle): JsonResponse
    {
        $request->validate([
            'fecha_inicio' => 'required|date|after:now',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);

        $result = $this->availabilityService->checkAvailability(
            $vehicle,
            $request->fecha_inicio,
            $request->fecha_fin
        );

        return $this->handleServiceResult($result);
    }

    /**
     * Obtener horario ocupado del vehículo
     */
    public function schedule(Request $request, Vehicle $vehicle): JsonResponse
    {
        $desde = $request->input('desde', now());
        $hasta = $request->input('hasta', now()->addDays(30));

        $result = $this->availabilityService->getVehicleSchedule(
            $vehicle->id,
            $desde,
            $hasta
        );

        return $this->handleServiceResult($result);
    }

    /**
     * Sugerir vehículos alternativos
     */
    public function alternatives(Request $request, Vehicle $vehicle): JsonResponse
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);

        $result = $this->availabilityService->suggestAlternatives(
            $vehicle->id,
            $request->fecha_inicio,
            $request->fecha_fin
        );

        return $this->handleServiceResult($result);
    }

    /**
     * Comparar precios entre vehículos
     */
    public function comparePrices(Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_ids' => 'required|array|min:2',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);

        $result = $this->pricingService->compareVehiclePrices(
            $request->vehicle_ids,
            $request->fecha_inicio,
            $request->fecha_fin
        );

        return $this->handleServiceResult($result);
    }

    /**
     * Estimación rápida de precio
     */
    public function quickEstimate(Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);

        $result = $this->pricingService->getQuickEstimate(
            $request->vehicle_id,
            $request->fecha_inicio,
            $request->fecha_fin
        );

        return $this->handleServiceResult($result);
    }

    /**
     * Vehículos por tipo
     */
    public function byType(Request $request, string $tipo): JsonResponse
    {
        $vehicles = Vehicle::disponibles()
            ->porTipo($tipo)
            ->with(['branch'])
            ->get();

        return $this->success([
            'tipo' => $tipo,
            'vehicles' => VehicleResource::collection($vehicles),
            'total' => $vehicles->count(),
        ]);
    }

    /**
     * Vehículos por sede
     */
    public function byBranch(Request $request, int $sedeId): JsonResponse
    {
        $vehicles = Vehicle::disponibles()
            ->porSede($sedeId)
            ->with(['branch'])
            ->get();

        return $this->success([
            'sede_id' => $sedeId,
            'vehicles' => VehicleResource::collection($vehicles),
            'total' => $vehicles->count(),
        ]);
    }

    /**
     * Ubicación actual del vehículo (telemetría)
     */
    public function location(Vehicle $vehicle): JsonResponse
    {
        $location = $vehicle->getCurrentLocation();

        if (!$location) {
            return $this->error('No hay información de ubicación disponible');
        }

        return $this->success($location);
    }

    /**
     * Estadísticas del vehículo (admin)
     */
    public function stats(Vehicle $vehicle): JsonResponse
    {
        
        $this->authorize('view-vehicle-stats');

        $stats = [
            'total_reservas' => $vehicle->reservations()->count(),
            'reservas_completadas' => $vehicle->reservations()->completadas()->count(),
            'total_ingresos' => $vehicle->getTotalIngresos(),
            'calificacion_promedio' => $vehicle->getAverageRating(),
            'tasa_ocupacion' => $this->availabilityService->getVehicleOccupancyRate($vehicle->id),
        ];

        return $this->success($stats);
    }
}