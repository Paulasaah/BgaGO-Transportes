<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RouteService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador API para cálculo de rutas con OSRM
 * 
 * Endpoints:
 * - POST /api/routes/calculate - Calcular ruta entre dos puntos
 * - POST /api/routes/match - Map matching de puntos GPS
 * - POST /api/routes/eta - Estimar tiempo de llegada
 * - POST /api/routes/optimize - Optimizar ruta con múltiples paradas
 * - POST /api/routes/matrix - Matriz de distancias
 * - GET /api/routes/health - Health check de OSRM
 */
class RouteController extends Controller
{
    /**
     * Constructor con inyección de dependencias
     */
    public function __construct(
        protected RouteService $routeService
    ) {}

    /**
     * Calcular ruta entre dos puntos
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function calculate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'origin_lat' => 'required|numeric|between:-90,90',
            'origin_lng' => 'required|numeric|between:-180,180',
            'dest_lat' => 'required|numeric|between:-90,90',
            'dest_lng' => 'required|numeric|between:-180,180',
            'waypoints' => 'sometimes|array',
            'waypoints.*.lat' => 'required_with:waypoints|numeric|between:-90,90',
            'waypoints.*.lng' => 'required_with:waypoints|numeric|between:-180,180',
            'options' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $result = $this->routeService->calculateRoute(
            $validated['origin_lat'],
            $validated['origin_lng'],
            $validated['dest_lat'],
            $validated['dest_lng'],
            $validated['waypoints'] ?? [],
            $validated['options'] ?? []
        );

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Map matching de puntos GPS a la red de carreteras
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function match(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'gps_points' => 'required|array|min:2',
            'gps_points.*.lat' => 'required|numeric|between:-90,90',
            'gps_points.*.lng' => 'required|numeric|between:-180,180',
            'options' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $result = $this->routeService->matchRoute(
            $validated['gps_points'],
            $validated['options'] ?? []
        );

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Estimar tiempo de llegada (ETA)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function estimateArrival(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_lat' => 'required|numeric|between:-90,90',
            'current_lng' => 'required|numeric|between:-180,180',
            'dest_lat' => 'required|numeric|between:-90,90',
            'dest_lng' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $result = $this->routeService->estimateArrival(
            $validated['current_lat'],
            $validated['current_lng'],
            $validated['dest_lat'],
            $validated['dest_lng']
        );

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Optimizar ruta con múltiples paradas
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function optimize(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_lat' => 'required|numeric|between:-90,90',
            'start_lng' => 'required|numeric|between:-180,180',
            'stops' => 'required|array|min:1',
            'stops.*.lat' => 'required|numeric|between:-90,90',
            'stops.*.lng' => 'required|numeric|between:-180,180',
            'end_lat' => 'sometimes|nullable|numeric|between:-90,90',
            'end_lng' => 'sometimes|nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $result = $this->routeService->optimizeRoute(
            $validated['start_lat'],
            $validated['start_lng'],
            $validated['stops'],
            $validated['end_lat'] ?? null,
            $validated['end_lng'] ?? null
        );

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Obtener matriz de distancias entre múltiples puntos
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function distanceMatrix(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sources' => 'required|array|min:1',
            'sources.*.lat' => 'required|numeric|between:-90,90',
            'sources.*.lng' => 'required|numeric|between:-180,180',
            'destinations' => 'sometimes|array',
            'destinations.*.lat' => 'required_with:destinations|numeric|between:-90,90',
            'destinations.*.lng' => 'required_with:destinations|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $result = $this->routeService->getDistanceMatrix(
            $validated['sources'],
            $validated['destinations'] ?? []
        );

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Health check de OSRM
     * 
     * @return JsonResponse
     */
    public function health(): JsonResponse
    {
        $result = $this->routeService->healthCheck();

        return response()->json($result, $result['success'] ? 200 : 503);
    }
}
