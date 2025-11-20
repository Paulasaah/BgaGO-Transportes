<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SimulationService;
use Illuminate\Http\JsonResponse;

/**
 * Endpoints para que el publisher IoT consuma datos de simulación.
 * No generan telemetría, solo exponen reservas/vehículos listos para simular.
 */
class SimulationController extends Controller
{
    public function __construct(
        protected SimulationService $simulationService
    ) {}

    /**
     * Listar servicios activos (reservas en curso) para simulación.
     */
    public function activeServices(): JsonResponse
    {
        $result = $this->simulationService->getActiveServicesForSimulation();

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Listar vehículos sin servicio activo (idle) para simulación.
     */
    public function idleVehicles(): JsonResponse
    {
        $result = $this->simulationService->getIdleVehiclesForSimulation();

        return response()->json($result, $result['success'] ? 200 : 500);
    }
}
