<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Delivery;
use App\Models\Vehicle;
use App\Enums\ReservationStatus;
use App\Enums\DeliveryType;
use Carbon\Carbon;
use Exception;
use App\Contracts\GeocodingService;

class DeliveryService extends BaseService
{
    protected PricingService $pricingService;
    protected RouteService $routeService;
    protected GeocodingService $geocodingService;

    public function __construct(
        PricingService $pricingService,
        RouteService $routeService,
        GeocodingService $geocodingService
    ) {
        $this->pricingService = $pricingService;
        $this->routeService = $routeService;
        $this->geocodingService = $geocodingService;
    }

    /**
     * Crear domicilio de paquete
     */
    public function createPackageDelivery(array $data): array
    {
        return $this->executeWithTransaction(function () use ($data) {
            // ✅ Validar campos requeridos
            $this->validateRequired($data, [
                'user_id',
                'sede_id',
                'direccion_origen',
                'direccion_destino',
            ]);

            // Intentar obtener coordenadas
            $hasOriginCoords = !empty($data['lat_origen']) && !empty($data['lon_origen']);
            $hasDestCoords = !empty($data['lat_destino']) && !empty($data['lon_destino']);

            if (!$hasOriginCoords && !empty($data['direccion_origen'])) {
                $geoOrigin = $this->geocodingService->geocode($data['direccion_origen']);
                if ($geoOrigin['success']) {
                    $data['lat_origen'] = $geoOrigin['lat'];
                    $data['lon_origen'] = $geoOrigin['lng'];
                } else {
                    $data['lat_origen'] = (float) (config('delivery.default_origin_lat', 7.119));
                    $data['lon_origen'] = (float) (config('delivery.default_origin_lng', -73.122));
                }
            }

            if (!$hasDestCoords && !empty($data['direccion_destino'])) {
                $geoDest = $this->geocodingService->geocode($data['direccion_destino']);
                if ($geoDest['success']) {
                    $data['lat_destino'] = $geoDest['lat'];
                    $data['lon_destino'] = $geoDest['lng'];
                } else {
                    $data['lat_destino'] = isset($data['lat_origen']) ? ((float) $data['lat_origen'] + 0.01) : (float) (config('delivery.default_origin_lat', 7.129));
                    $data['lon_destino'] = isset($data['lon_origen']) ? ((float) $data['lon_origen'] + 0.01) : (float) (config('delivery.default_origin_lng', -73.112));
                }
            }

            // ✅ Calcular distancia y tiempo usando OSRM
            $origenCoords = $this->parseCoordinates($data['lat_origen'] . ',' . $data['lon_origen']);
            $destinoCoords = $this->parseCoordinates($data['lat_destino'] . ',' . $data['lon_destino']);

            $routeResult = $this->routeService->calculateRoute(
                $origenCoords['lat'],
                $origenCoords['lng'],
                $destinoCoords['lat'],
                $destinoCoords['lng']
            );

            if ($routeResult['success']) {
                $route = $routeResult['data'];
                $distanciaKm = $route['distance_km'];
                $tiempoEstimado = $route['duration_minutes'];
            } else {
                $distanciaKm = $this->calculateDistance($origenCoords['lat'], $origenCoords['lng'], $destinoCoords['lat'], $destinoCoords['lng']);
                $tiempoEstimado = $this->calculateEstimatedTime($distanciaKm);
                $route = [
                    'distance_km' => $distanciaKm,
                    'duration_minutes' => $tiempoEstimado,
                    'geometry' => [],
                ];
            }

            // ✅ Ajustar tiempo estimado usando una velocidad promedio de ciudad
            // Esto evita tiempos demasiado optimistas de OSRM (ej. 2-3 minutos para trayectos urbanos largos)
            $citySpeed = (float) config('delivery.city_speed_kmh', 25);
            if ($citySpeed > 0 && $distanciaKm > 0) {
                $minCityDuration = (int) ceil(($distanciaKm / $citySpeed) * 60); // minutos
                if ($minCityDuration > $tiempoEstimado) {
                    $tiempoEstimado = $minCityDuration;
                }
            }

            // ✅ Calcular hora de recogida y fin estimado a partir de OSRM
            $pickupAt = isset($data['fecha_recogida'])
                ? Carbon::parse($data['fecha_recogida'])
                : now();

            $estimatedEndAt = (clone $pickupAt)->addMinutes($tiempoEstimado);

            // ✅ Calcular precio
            $pricing = $this->pricingService->calculateDeliveryPrice(
                DeliveryType::Paquete,
                $distanciaKm
            );

            // ✅ Crear reserva base
            $reservation = Reservation::create([
                'codigo' => Reservation::generateCode(),
                'user_id' => $data['user_id'],
                'sede_id' => $data['sede_id'],
                'tipo' => \App\Enums\ReservationType::Domicilio,
                'estado' => ReservationStatus::Pendiente,
                'origen_direccion' => $data['direccion_origen'],
                'origen_lat' => $origenCoords['lat'],
                'origen_lng' => $origenCoords['lng'],
                'destino_direccion' => $data['direccion_destino'],
                'destino_lat' => $destinoCoords['lat'],
                'destino_lng' => $destinoCoords['lng'],
                'waypoints' => $route['geometry'] ?? [],
                'distancia_km' => $distanciaKm,
                'duracion_minutos' => $tiempoEstimado,
                'fecha_inicio' => $pickupAt,
                'fecha_fin' => $estimatedEndAt,
                'monto' => $pricing['data']['subtotal'],
                'descuento' => $pricing['data']['descuento'] ?? 0,
                'monto_final' => $pricing['data']['total'],
                'notas_cliente' => $data['instrucciones_especiales'] ?? null,
            ]);

            // ✅ Crear registro en deliveries
            $delivery = Delivery::create([
                'user_id' => $data['user_id'],
                'reserva_id' => $reservation->id,
                'tipo' => DeliveryType::Paquete,

                // Direcciones
                'direccion_origen' => $data['direccion_origen'],
                'lat_origen' => $data['lat_origen'],
                'lon_origen' => $data['lon_origen'],
                'direccion_destino' => $data['direccion_destino'],
                'lat_destino' => $data['lat_destino'],
                'lon_destino' => $data['lon_destino'],

                // Datos de contacto (opcionales en flujos admin)
                'nombre_remitente' => $data['nombre_remitente'] ?? null,
                'telefono_remitente' => $data['telefono_remitente'] ?? null,
                'nombre_destinatario' => $data['nombre_destinatario'] ?? null,
                'telefono_destinatario' => $data['telefono_destinatario'] ?? null,

                // Detalles adicionales
                'descripcion_contenido' => $data['descripcion_contenido'] ?? null,
                'peso_estimado' => $data['peso_estimado'] ?? ($data['peso_kg'] ?? null),
                'requiere_firma' => $data['requiere_firma'] ?? false,
                'es_fragil' => $data['es_fragil'] ?? false,
                'instrucciones_especiales' => $data['instrucciones_especiales'] ?? null,
                'costo' => $data['costo'] ?? ($pricing['data']['total'] ?? 0),
                'fecha_entrega_estimada' => $estimatedEndAt,
            ]);

            return $delivery->load(['user', 'reservation.branch']);
        }, 'crear_domicilio_paquete');
    }

    /**
     * Asignar conductor a domicilio
     */
    public function assignDriver(int $deliveryId, int $conductorId): array
    {
        return $this->executeWithTransaction(function () use ($deliveryId, $conductorId) {
            $delivery = Delivery::findOrFail($deliveryId);

            if ($delivery->conductor_id) {
                throw new Exception('Ya existe un conductor asignado');
            }

            $delivery->update([
                'conductor_id' => $conductorId,
                'estado' => 'asignado', // el servicio queda asignado al conductor, pero aún no en camino
            ]);

            if ($delivery->reservation) {
                $delivery->reservation->update([
                    'conductor_id' => $conductorId,
                    'estado' => ReservationStatus::Confirmada,
                    'fecha_confirmacion' => now(),
                ]);
            }

            return $delivery->fresh()->load('reservation.driver');
        }, 'asignar_conductor');
    }

    /**
     * Iniciar domicilio (conductor o admin)
     */
    public function startDelivery(int $deliveryId): array
    {
        return $this->executeWithTransaction(function () use ($deliveryId) {
            $delivery = Delivery::findOrFail($deliveryId);
            $user = auth()->user();

            // 🛡️ Admin puede iniciar cualquier entrega sin validaciones de estado
            if ($user && $user->hasRole(['admin', 'super_admin'])) {
                // ✅ Si ya está iniciado, retornar sin error
                if ($delivery->estado === 'en_camino') {
                    return $delivery->fresh();
                }

                $delivery->update([
                    'estado' => 'en_camino',
                    'fecha_recogida' => $delivery->fecha_recogida ?? now(),
                ]);

                // Actualizar reserva asociada
                if ($delivery->reservation && !$delivery->reservation->isActiva()) {
                    $delivery->reservation->update([
                        'estado' => ReservationStatus::Activa,
                        'fecha_inicio_real' => $delivery->reservation->fecha_inicio_real ?? now(),
                    ]);
                }

                return $delivery->fresh();
            }

            // 🚗 Conductor: Validar estados permitidos
            $permitidos = ['pendiente', 'asignado', 'confirmado'];

            // ✅ IDEMPOTENCIA: Si ya está en camino, asegurar que tenga fecha_recogida
            if ($delivery->estado === 'en_camino') {
                // Si no tiene fecha_recogida, agregarla ahora
                if (!$delivery->fecha_recogida) {
                    $delivery->update(['fecha_recogida' => now()]);
                }
                return $delivery->fresh();
            }

            if (!in_array($delivery->estado, $permitidos)) {
                throw new Exception(
                    "Solo se pueden iniciar domicilios en estados: " .
                    implode(', ', $permitidos) .
                    ". Estado actual: {$delivery->estado}"
                );
            }

            $delivery->update([
                'estado' => 'en_camino',
                'fecha_recogida' => now(),
            ]);

            // Actualizar reserva asociada
            if ($delivery->reservation) {
                $delivery->reservation->update([
                    'estado' => ReservationStatus::Activa,
                    'fecha_inicio_real' => now(),
                ]);
            }

            return $delivery->fresh();
        }, 'iniciar_domicilio');
    }

    /**
     * Completar entrega
     */
    public function completeDelivery(int $deliveryId, array $completionData): array
    {
        return $this->executeWithTransaction(function () use ($deliveryId, $completionData) {
            $delivery = Delivery::findOrFail($deliveryId);
            $user = auth()->user();

            // 👑 Admin puede completar cualquier entrega
            if ($user && $user->hasRole(['admin', 'super_admin'])) {
                $delivery->update([
                    'estado' => 'entregado',
                    'fecha_entrega' => now(),
                    'firma_destinatario' => $completionData['firma'] ?? null,
                    'foto_entrega' => $completionData['foto'] ?? null,
                    'notas_entrega' => $completionData['notas'] ?? null,
                ]);
                return $delivery->fresh();
            }

            // 🚗 Validar estados válidos para completar
            $permitidos = ['en_camino'];
            if (!in_array($delivery->estado, $permitidos)) {
                throw new Exception('Solo se pueden completar domicilios en curso');
            }

            $delivery->update([
                'estado' => 'entregado',
                'fecha_entrega' => now(),
                'firma_destinatario' => $completionData['firma'] ?? null,
                'foto_entrega' => $completionData['foto'] ?? null,
                'notas_entrega' => $completionData['notas'] ?? null,
            ]);

            // Actualizar reserva asociada
            if ($delivery->reservation) {
                $delivery->reservation->update([
                    'estado' => ReservationStatus::Completada,
                    'fecha_fin_real' => now(),
                ]);
            }

            // Liberar vehículo si aplica
            if ($delivery->vehicle && $delivery->vehicle->isOcupado()) {
                $delivery->vehicle->update(['estado' => 'disponible']);
            }

            return $delivery->fresh();
        }, 'completar_domicilio');
    }

    /**
     * Obtener domicilios activos del usuario
     */
    public function getUserActiveDeliveries(int $userId): array
    {
        return $this->execute(function () use ($userId) {
            return Delivery::where('user_id', $userId)
                ->whereIn('estado', ['pendiente', 'en_camino'])
                ->with(['reservation', 'vehicle'])
                ->orderByDesc('created_at')
                ->get();
        }, 'obtener_domicilios_activos_usuario');
    }

    /**
     * Rastrear domicilio
     */
    public function trackDelivery(int $deliveryId): array
    {
        return $this->execute(function () use ($deliveryId) {
            $delivery = Delivery::with([
                'reservation.driver',
                'reservation.gpsTracks' => fn($q) => $q->latest()->limit(1),
            ])->findOrFail($deliveryId);

            $lastTrack = $delivery->reservation->gpsTracks->first();

            return [
                'delivery' => $delivery,
                'estado' => $delivery->estado,
                'ultima_ubicacion' => $lastTrack ? [
                    'lat' => $lastTrack->latitud,
                    'lng' => $lastTrack->longitud,
                    'velocidad' => $lastTrack->velocidad,
                    'timestamp' => $lastTrack->fecha_registro,
                ] : null,
            ];
        }, 'rastrear_domicilio');
    }
}
