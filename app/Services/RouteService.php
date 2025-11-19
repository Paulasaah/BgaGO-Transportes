<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Servicio para cálculo de rutas usando OSRM
 * 
 * Responsabilidades:
 * - Calcular rutas entre dos o más puntos
 * - Map matching de puntos GPS a carreteras
 * - Estimar tiempos de llegada (ETA)
 * - Optimizar rutas con múltiples paradas
 * 
 * @see https://project-osrm.org/docs/v5.24.0/api/
 */
class RouteService extends BaseService
{
    /**
     * Configuración de OSRM
     */
    protected string $osrmHost;
    protected int $osrmPort;
    protected string $osrmProfile;
    protected int $osrmTimeout;
    protected string $baseUrl;

    /**
     * Constructor - Inicializa configuración desde config/services.php
     */
    public function __construct()
    {
        $this->osrmHost = config('services.osrm.host', 'osrm');
        $this->osrmPort = config('services.osrm.port', 5000);
        $this->osrmProfile = config('services.osrm.profile', 'driving');
        $this->osrmTimeout = config('services.osrm.timeout', 10);
        $this->baseUrl = "http://{$this->osrmHost}:{$this->osrmPort}";
    }

    /**
     * Calcular ruta entre dos puntos con waypoints opcionales
     * 
     * @param float $originLat Latitud del origen
     * @param float $originLng Longitud del origen
     * @param float $destLat Latitud del destino
     * @param float $destLng Longitud del destino
     * @param array $waypoints Puntos intermedios [['lat' => x, 'lng' => y], ...]
     * @param array $options Opciones adicionales (steps, alternatives, etc.)
     * @return array ['success' => bool, 'data' => array, 'message' => string]
     */
    public function calculateRoute(
        float $originLat,
        float $originLng,
        float $destLat,
        float $destLng,
        array $waypoints = [],
        array $options = []
    ): array {
        return $this->execute(function () use ($originLat, $originLng, $destLat, $destLng, $waypoints, $options) {
            
            // Validar coordenadas
            $this->validateCoordinates($originLat, $originLng, 'origen');
            $this->validateCoordinates($destLat, $destLng, 'destino');

            // Construir string de coordenadas: lng,lat;lng,lat;...
            $coords = $this->buildCoordinatesString($originLat, $originLng, $destLat, $destLng, $waypoints);

            // Generar cache key
            $cacheKey = $this->generateCacheKey('route', $coords, $options);

            // Intentar obtener de cache (5 minutos)
            return Cache::remember($cacheKey, 300, function () use ($coords, $options) {
                return $this->fetchRouteFromOSRM($coords, $options);
            });

        }, 'calcular_ruta_osrm');
    }

    /**
     * Map matching: Ajustar puntos GPS a la red de carreteras
     * 
     * Útil para tracking de vehículos en tiempo real
     * 
     * @param array $gpsPoints Array de puntos GPS [['lat' => x, 'lng' => y], ...]
     * @param array $options Opciones adicionales
     * @return array ['success' => bool, 'data' => array, 'message' => string]
     */
    public function matchRoute(array $gpsPoints, array $options = []): array
    {
        return $this->execute(function () use ($gpsPoints, $options) {
            
            if (count($gpsPoints) < 2) {
                throw new Exception('Se requieren al menos 2 puntos GPS para map matching');
            }

            // Validar todos los puntos
            foreach ($gpsPoints as $index => $point) {
                if (!isset($point['lat']) || !isset($point['lng'])) {
                    throw new Exception("Punto GPS #{$index} inválido: requiere 'lat' y 'lng'");
                }
                $this->validateCoordinates($point['lat'], $point['lng'], "punto #{$index}");
            }

            // Construir string de coordenadas
            $coords = collect($gpsPoints)
                ->map(fn($p) => "{$p['lng']},{$p['lat']}")
                ->join(';');

            // Llamar a OSRM Match API
            $url = "{$this->baseUrl}/match/v1/{$this->osrmProfile}/{$coords}";
            
            $response = Http::timeout($this->osrmTimeout)
                ->connectTimeout(5)
                ->get($url, array_merge([
                    'overview' => 'full',
                    'geometries' => 'geojson',
                    'steps' => false,
                    'annotations' => true,
                ], $options));

            if (!$response->successful()) {
                throw new Exception("Error de conexión con OSRM: HTTP {$response->status()}");
            }

            $data = $response->json();

            if ($data['code'] !== 'Ok') {
                throw new Exception("OSRM error: {$data['code']} - " . ($data['message'] ?? 'Unknown error'));
            }

            $matching = $data['matchings'][0] ?? null;

            if (!$matching) {
                throw new Exception('No se pudo hacer match de los puntos GPS a la red de carreteras');
            }

            return [
                'distance_km' => round($matching['distance'] / 1000, 2),
                'duration_minutes' => round($matching['duration'] / 60, 0),
                'geometry' => $matching['geometry'],
                'coordinates' => $matching['geometry']['coordinates'],
                'confidence' => $matching['confidence'] ?? 0,
                'matched_points' => count($gpsPoints),
            ];

        }, 'match_route_osrm');
    }

    /**
     * Estimar tiempo de llegada desde posición actual a destino
     * 
     * @param float $currentLat Latitud actual
     * @param float $currentLng Longitud actual
     * @param float $destLat Latitud del destino
     * @param float $destLng Longitud del destino
     * @return array ['success' => bool, 'data' => array, 'message' => string]
     */
    public function estimateArrival(
        float $currentLat,
        float $currentLng,
        float $destLat,
        float $destLng
    ): array {
        $route = $this->calculateRoute($currentLat, $currentLng, $destLat, $destLng);

        if (!$route['success']) {
            return $route;
        }

        $routeData = $route['data'];

        return $this->success([
            'eta_minutes' => $routeData['duration_minutes'],
            'eta_datetime' => now()->addMinutes($routeData['duration_minutes'])->toIso8601String(),
            'eta_datetime_formatted' => now()->addMinutes($routeData['duration_minutes'])->format('Y-m-d H:i:s'),
            'distance_remaining_km' => $routeData['distance_km'],
            'current_time' => now()->toIso8601String(),
        ], 'ETA calculado exitosamente');
    }

    /**
     * Optimizar ruta con múltiples paradas (Traveling Salesman Problem)
     * 
     * @param float $startLat Latitud de inicio
     * @param float $startLng Longitud de inicio
     * @param array $stops Paradas a visitar [['lat' => x, 'lng' => y], ...]
     * @param float|null $endLat Latitud de fin (opcional, si null regresa al inicio)
     * @param float|null $endLng Longitud de fin (opcional)
     * @return array ['success' => bool, 'data' => array, 'message' => string]
     */
    public function optimizeRoute(
        float $startLat,
        float $startLng,
        array $stops,
        ?float $endLat = null,
        ?float $endLng = null
    ): array {
        return $this->execute(function () use ($startLat, $startLng, $stops, $endLat, $endLng) {
            
            if (count($stops) < 1) {
                throw new Exception('Se requiere al menos 1 parada para optimizar');
            }

            // Validar coordenadas de inicio
            $this->validateCoordinates($startLat, $startLng, 'inicio');

            // Construir coordenadas: inicio + paradas + fin (si existe)
            $coords = "{$startLng},{$startLat}";
            
            foreach ($stops as $index => $stop) {
                if (!isset($stop['lat']) || !isset($stop['lng'])) {
                    throw new Exception("Parada #{$index} inválida: requiere 'lat' y 'lng'");
                }
                $this->validateCoordinates($stop['lat'], $stop['lng'], "parada #{$index}");
                $coords .= ";{$stop['lng']},{$stop['lat']}";
            }

            // Si hay punto final diferente, agregarlo
            if ($endLat !== null && $endLng !== null) {
                $this->validateCoordinates($endLat, $endLng, 'fin');
                $coords .= ";{$endLng},{$endLat}";
            }

            // Llamar a OSRM Trip API (optimiza el orden de las paradas)
            $url = "{$this->baseUrl}/trip/v1/{$this->osrmProfile}/{$coords}";
            
            $response = Http::timeout($this->osrmTimeout)
                ->connectTimeout(5)
                ->get($url, [
                    'overview' => 'full',
                    'geometries' => 'geojson',
                    'steps' => true,
                    'source' => 'first', // Empezar desde el primer punto
                    'destination' => $endLat !== null ? 'last' : 'first', // Terminar en el último o regresar al primero
                ]);

            if (!$response->successful()) {
                throw new Exception("Error de conexión con OSRM: HTTP {$response->status()}");
            }

            $data = $response->json();

            if ($data['code'] !== 'Ok') {
                throw new Exception("OSRM error: {$data['code']} - " . ($data['message'] ?? 'Unknown error'));
            }

            $trip = $data['trips'][0] ?? null;

            if (!$trip) {
                throw new Exception('No se pudo optimizar la ruta');
            }

            // Extraer orden optimizado de las paradas
            $waypoints = collect($data['waypoints'])
                ->map(fn($wp) => [
                    'waypoint_index' => $wp['waypoint_index'],
                    'trips_index' => $wp['trips_index'],
                    'location' => $wp['location'],
                ])
                ->toArray();

            return [
                'distance_km' => round($trip['distance'] / 1000, 2),
                'duration_minutes' => round($trip['duration'] / 60, 0),
                'geometry' => $trip['geometry'],
                'coordinates' => $trip['geometry']['coordinates'],
                'optimized_order' => $waypoints,
                'total_stops' => count($stops),
            ];

        }, 'optimizar_ruta_osrm');
    }

    /**
     * Obtener tabla de distancias entre múltiples puntos (matriz de distancias)
     * 
     * @param array $sources Puntos de origen [['lat' => x, 'lng' => y], ...]
     * @param array $destinations Puntos de destino [['lat' => x, 'lng' => y], ...]
     * @return array ['success' => bool, 'data' => array, 'message' => string]
     */
    public function getDistanceMatrix(array $sources, array $destinations = []): array
    {
        return $this->execute(function () use ($sources, $destinations) {
            
            if (count($sources) < 1) {
                throw new Exception('Se requiere al menos 1 punto de origen');
            }

            // Si no hay destinos, usar los mismos puntos como destinos
            if (empty($destinations)) {
                $destinations = $sources;
            }

            // Construir coordenadas
            $coords = collect($sources)
                ->merge($destinations)
                ->unique(fn($p) => "{$p['lat']},{$p['lng']}")
                ->map(fn($p) => "{$p['lng']},{$p['lat']}")
                ->join(';');

            // Índices de sources y destinations
            $sourcesIndices = implode(';', range(0, count($sources) - 1));
            $destinationsIndices = implode(';', range(count($sources), count($sources) + count($destinations) - 1));

            // Llamar a OSRM Table API
            $url = "{$this->baseUrl}/table/v1/{$this->osrmProfile}/{$coords}";
            
            $response = Http::timeout($this->osrmTimeout)
                ->connectTimeout(5)
                ->get($url, [
                    'sources' => $sourcesIndices,
                    'destinations' => $destinationsIndices,
                ]);

            if (!$response->successful()) {
                throw new Exception("Error de conexión con OSRM: HTTP {$response->status()}");
            }

            $data = $response->json();

            if ($data['code'] !== 'Ok') {
                throw new Exception("OSRM error: {$data['code']} - " . ($data['message'] ?? 'Unknown error'));
            }

            // Convertir distancias a km y duraciones a minutos
            $distances = collect($data['distances'])
                ->map(fn($row) => collect($row)->map(fn($d) => round($d / 1000, 2))->toArray())
                ->toArray();

            $durations = collect($data['durations'])
                ->map(fn($row) => collect($row)->map(fn($d) => round($d / 60, 0))->toArray())
                ->toArray();

            return [
                'distances_km' => $distances,
                'durations_minutes' => $durations,
                'sources_count' => count($sources),
                'destinations_count' => count($destinations),
            ];

        }, 'matriz_distancias_osrm');
    }

    // ========================================
    // MÉTODOS PRIVADOS / HELPERS
    // ========================================

    /**
     * Construir string de coordenadas para OSRM
     */
    private function buildCoordinatesString(
        float $originLat,
        float $originLng,
        float $destLat,
        float $destLng,
        array $waypoints
    ): string {
        $coords = "{$originLng},{$originLat}";
        
        foreach ($waypoints as $wp) {
            if (!isset($wp['lat']) || !isset($wp['lng'])) {
                throw new Exception('Waypoint inválido: requiere lat y lng');
            }
            $this->validateCoordinates($wp['lat'], $wp['lng'], 'waypoint');
            $coords .= ";{$wp['lng']},{$wp['lat']}";
        }
        
        $coords .= ";{$destLng},{$destLat}";

        return $coords;
    }

    /**
     * Hacer request a OSRM Route API
     */
    private function fetchRouteFromOSRM(string $coords, array $options): array
    {
        $url = "{$this->baseUrl}/route/v1/{$this->osrmProfile}/{$coords}";
        
        $defaultOptions = [
            // Nivel de detalle de la geometría. "full" es más preciso y útil para el mapa.
            'overview' => 'full',
            // Formato GeoJSON, que es el que consume directamente Leaflet.
            'geometries' => 'geojson',
        ];

        $queryParams = array_merge($defaultOptions, $options);

        Log::info("🗺️ Calculando ruta con OSRM", [
            'url' => $url,
            'params' => $queryParams,
        ]);

        $response = Http::timeout($this->osrmTimeout)
            ->connectTimeout(5)
            ->retry(2, 500) // 2 intentos con 500ms entre ellos
            ->get($url, $queryParams);

        if (!$response->successful()) {
            throw new Exception("Error de conexión con OSRM: HTTP {$response->status()}");
        }

        $data = $response->json();

        if ($data['code'] !== 'Ok') {
            throw new Exception("OSRM error: {$data['code']} - " . ($data['message'] ?? 'Unknown error'));
        }

        $route = $data['routes'][0] ?? null;

        if (!$route) {
            throw new Exception('No se encontró una ruta válida');
        }

        return [
            'distance_km' => round($route['distance'] / 1000, 2),
            'duration_minutes' => round($route['duration'] / 60, 0),
            'geometry' => $route['geometry'],
            'coordinates' => $route['geometry']['coordinates'],
            'steps' => $this->formatSteps($route['legs'][0]['steps'] ?? []),
            'legs' => $this->formatLegs($route['legs'] ?? []),
            'waypoints' => $data['waypoints'] ?? [],
        ];
    }

    /**
     * Formatear pasos de navegación
     */
    private function formatSteps(array $steps): array
    {
        return collect($steps)->map(function ($step) {
            return [
                'instruction' => $step['maneuver']['instruction'] ?? 'Continuar',
                'distance_m' => round($step['distance'], 0),
                'duration_s' => round($step['duration'], 0),
                'type' => $step['maneuver']['type'] ?? 'turn',
                'modifier' => $step['maneuver']['modifier'] ?? null,
                'name' => $step['name'] ?? 'Calle sin nombre',
                'mode' => $step['mode'] ?? 'driving',
            ];
        })->toArray();
    }

    /**
     * Formatear legs (tramos) de la ruta
     */
    private function formatLegs(array $legs): array
    {
        return collect($legs)->map(function ($leg) {
            return [
                'distance_km' => round($leg['distance'] / 1000, 2),
                'duration_minutes' => round($leg['duration'] / 60, 0),
                'steps_count' => count($leg['steps'] ?? []),
            ];
        })->toArray();
    }

    /**
     * Validar que las coordenadas sean válidas
     */
    private function validateCoordinates(float $lat, float $lng, string $context = 'coordenada'): void
    {
        if ($lat < -90 || $lat > 90) {
            throw new Exception("Latitud inválida para {$context}: {$lat} (debe estar entre -90 y 90)");
        }

        if ($lng < -180 || $lng > 180) {
            throw new Exception("Longitud inválida para {$context}: {$lng} (debe estar entre -180 y 180)");
        }

        // Validación específica para Colombia
        // Colombia está entre: lat 12.5°N a -4.2°S, lng -66.8°W a -79.0°W
        if ($lat < -5 || $lat > 13) {
            Log::warning("⚠️ Coordenada fuera de Colombia", [
                'context' => $context,
                'lat' => $lat,
                'lng' => $lng,
            ]);
        }

        if ($lng < -80 || $lng > -66) {
            Log::warning("⚠️ Coordenada fuera de Colombia", [
                'context' => $context,
                'lat' => $lat,
                'lng' => $lng,
            ]);
        }
    }

    /**
     * Generar cache key único
     */
    private function generateCacheKey(string $operation, string $coords, array $options): string
    {
        $optionsHash = md5(json_encode($options));
        return "osrm:{$operation}:{$this->osrmProfile}:" . md5($coords) . ":{$optionsHash}";
    }

    /**
     * Verificar si OSRM está disponible
     */
    public function healthCheck(): array
    {
        try {
            // Hacer una consulta simple para verificar conectividad
            $testCoords = "-73.1227,7.1193;-73.0889,7.0652"; // Bucaramanga a Floridablanca
            $url = "{$this->baseUrl}/route/v1/{$this->osrmProfile}/{$testCoords}";
            
            $response = Http::timeout(5)->get($url, ['overview' => 'false']);

            if ($response->successful() && $response->json('code') === 'Ok') {
                return $this->success([
                    'status' => 'healthy',
                    'host' => $this->osrmHost,
                    'port' => $this->osrmPort,
                    'profile' => $this->osrmProfile,
                    'response_time_ms' => $response->handlerStats()['total_time'] ?? 0,
                ], 'OSRM está funcionando correctamente');
            }

            return $this->error('OSRM respondió pero con error', [
                'status' => 'unhealthy',
                'code' => $response->json('code'),
            ]);

        } catch (Exception $e) {
            return $this->error('No se pudo conectar con OSRM: ' . $e->getMessage(), [
                'status' => 'down',
                'host' => $this->osrmHost,
                'port' => $this->osrmPort,
            ]);
        }
    }
}
