<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\BranchResource;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BranchController extends BaseApiController
{
    /**
     * Listar todas las sedes
     */
    public function index(Request $request): JsonResponse
    {
        $includeVehicles = $request->boolean('include_vehicles');
        $includeStats = $request->boolean('include_stats');

        $branches = Branch::when($includeVehicles, function($q) {
            $q->with(['vehicles' => function($query) {
                $query->disponibles();
            }]);
        })->get();

        return $this->success(
            BranchResource::collection($branches)
        );
    }

    /**
     * Detalle de sede
     */
    public function show(Request $request, Branch $branch): JsonResponse
    {
        $includeVehicles = $request->boolean('include_vehicles');
        $includeStats = $request->boolean('include_stats');

        if ($includeVehicles) {
            $branch->load(['vehicles' => function($query) {
                $query->disponibles();
            }]);
        }

        return $this->success(
            new BranchResource($branch)
        );
    }

    /**
     * Sede más cercana a coordenadas
     */
    public function nearest(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lon' => 'required|numeric|between:-180,180',
        ]);

        $lat = $request->lat;
        $lon = $request->lon;

        // Fórmula Haversine en MySQL
        $branches = Branch::selectRaw("
            *,
            (6371 * acos(cos(radians(?)) 
            * cos(radians(lat)) 
            * cos(radians(lon) - radians(?)) 
            + sin(radians(?)) 
            * sin(radians(lat)))) AS distancia_km
        ", [$lat, $lon, $lat])
        ->orderBy('distancia_km')
        ->first();

        if (!$branches) {
            return $this->notFound('No se encontraron sedes');
        }

        return $this->success([
            'branch' => new BranchResource($branches),
            'distancia_km' => round($branches->distancia_km, 2),
        ]);
    }

    /**
     * Sedes en radio de distancia
     */
    public function inRadius(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lon' => 'required|numeric|between:-180,180',
            'radius_km' => 'nullable|numeric|min:1|max:50',
        ]);

        $lat = $request->lat;
        $lon = $request->lon;
        $radiusKm = $request->input('radius_km', 10); // 10km por defecto

        $branches = Branch::selectRaw("
            *,
            (6371 * acos(cos(radians(?)) 
            * cos(radians(lat)) 
            * cos(radians(lon) - radians(?)) 
            + sin(radians(?)) 
            * sin(radians(lat)))) AS distancia_km
        ", [$lat, $lon, $lat])
        ->having('distancia_km', '<=', $radiusKm)
        ->orderBy('distancia_km')
        ->get();

        return $this->success([
            'branches' => BranchResource::collection($branches),
            'total' => $branches->count(),
            'radius_km' => $radiusKm,
        ]);
    }

    /**
     * Vehículos disponibles en sede
     */
    public function vehicles(Branch $branch): JsonResponse
    {
        $vehicles = $branch->vehicles()
            ->disponibles()
            ->get();

        return $this->success([
            'branch' => new BranchResource($branch),
            'vehicles' => \App\Http\Resources\VehicleResource::collection($vehicles),
            'total_disponibles' => $vehicles->count(),
        ]);
    }

    /**
     * Estadísticas de la sede
     */
    public function stats(Branch $branch): JsonResponse
    {
        $stats = [
            'capacidad_total' => $branch->capacidad_vehiculos,
            'vehiculos_actuales' => $branch->vehicles()->count(),
            'vehiculos_disponibles' => $branch->vehicles()->disponibles()->count(),
            'vehiculos_ocupados' => $branch->vehicles()->ocupados()->count(),
            'vehiculos_mantenimiento' => $branch->vehicles()->enMantenimiento()->count(),
            'ocupacion_porcentaje' => $branch->getOccupancyPercentage(),
            'reservas_activas' => $branch->reservations()
                ->whereIn('estado', ['confirmada', 'activa'])
                ->count(),
        ];

        return $this->success($stats);
    }
}