<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Delivery;
use App\Models\Vehicle;
use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
use App\Enums\DeliveryType;
use Carbon\Carbon;
use Exception;

class DeliveryService extends BaseService
{
    protected PricingService $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Crear domicilio de paquete
     */
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
                'nombre_remitente',
                'telefono_remitente',
                'nombre_destinatario',
                'telefono_destinatario',
            ]);

            // ✅ Calcular distancia y tiempo
            $origenCoords = $this->parseCoordinates($data['lat_origen'] . ',' . $data['lon_origen']);
            $destinoCoords = $this->parseCoordinates($data['lat_destino'] . ',' . $data['lon_destino']);

            $distanciaKm = $this->calculateDistance(
                $origenCoords['lat'],
                $origenCoords['lng'],
                $destinoCoords['lat'],
                $destinoCoords['lng']
            );

            $tiempoEstimado = $this->calculateEstimatedTime($distanciaKm);

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
                'tipo' => ReservationType::Domicilio,
                'estado' => ReservationStatus::Pendiente,
                'origen_direccion' => $data['direccion_origen'],
                'origen_lat' => $origenCoords['lat'],
                'origen_lng' => $origenCoords['lng'],
                'destino_direccion' => $data['direccion_destino'],
                'destino_lat' => $destinoCoords['lat'],
                'destino_lng' => $destinoCoords['lng'],
                'distancia_km' => $distanciaKm,
                'duracion_minutos' => $tiempoEstimado,
                'fecha_inicio' => Carbon::parse($data['fecha_recogida'] ?? now()),
                'fecha_fin' => Carbon::parse($data['fecha_recogida'] ?? now())->addMinutes($tiempoEstimado),
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

                // Agregados para dirección
                'direccion_origen' => $data['direccion_origen'],
                'lat_origen' => $data['lat_origen'],
                'lon_origen' => $data['lon_origen'],
                'direccion_destino' => $data['direccion_destino'],
                'lat_destino' => $data['lat_destino'],
                'lon_destino' => $data['lon_destino'],

                'nombre_remitente' => $data['nombre_remitente'],
                'telefono_remitente' => $data['telefono_remitente'],
                'nombre_destinatario' => $data['nombre_destinatario'],
                'telefono_destinatario' => $data['telefono_destinatario'],
                'descripcion_contenido' => $data['descripcion_contenido'] ?? null,
                'peso_estimado' => $data['peso_kg'] ?? null,
                'requiere_firma' => $data['requiere_firma'] ?? false,
                'es_fragil' => $data['es_fragil'] ?? false,
                'instrucciones_especiales' => $data['instrucciones_especiales'] ?? null,
                'costo' => $data['costo'] ?? $pricing['data']['total'] ?? 0, // ✅ agregado
            ]);

            return $delivery->load(['user', 'reservation.branch']);
        }, 'crear_domicilio_paquete');
    }



    /**
     * Crear domicilio de vehículo
     */
    public function createVehicleDelivery(array $data): array
    {
        return $this->executeWithTransaction(function () use ($data) {
            // Validar campos requeridos
            $this->validateRequired($data, [
                'user_id',
                'vehiculo_id',
                'sede_id',
                'destino_direccion',
                'nombre_destinatario',
                'telefono_destinatario',
            ]);

            // Verificar vehículo disponible
            $vehicle = Vehicle::findOrFail($data['vehiculo_id']);
            if (!$vehicle->isDisponible()) {
                throw new Exception('El vehículo no está disponible');
            }

            // Obtener sede como origen
            $origenCoords = [
                'lat' => $data['lat_origen'],
                'lng' => $data['lng_destino']
            ];

            $destinoCoords = $this->parseCoordinates($data['destino_lat'] . ',' . $data['destino_lng']);

            $distanciaKm = $this->calculateDistance(
                $origenCoords['lat'],
                $origenCoords['lng'],
                $destinoCoords['lat'],
                $destinoCoords['lng']
            );

            $tiempoEstimado = $this->calculateEstimatedTime($distanciaKm);

            // Calcular precio
            $pricing = $this->pricingService->calculateDeliveryPrice(
                DeliveryType::Vehiculo,
                $distanciaKm,
                $vehicle
            );

            // Crear reserva
            $reservation = Reservation::create([
                'codigo' => Reservation::generateCode(),
                'user_id' => $data['user_id'],
                'vehiculo_id' => $vehicle->id,
                'sede_id' => $data['sede_id'],
                'tipo' => ReservationType::Domicilio,
                'estado' => ReservationStatus::Pendiente,
                'direccion_origen' => $data['direccion_origen'],
                'lat_origen' => $origenCoords['lat'],
                'lng_destino' => $origenCoords['lng'],
                'direccion_destino' => $data['direccion_destino'],
                'destino_lat' => $destinoCoords['lat'],
                'destino_lng' => $destinoCoords['lng'],
                'distancia_km' => $distanciaKm,
                'duracion_minutos' => $tiempoEstimado,
                'fecha_inicio' => Carbon::parse($data['fecha_entrega'] ?? now()),
                'fecha_fin' => Carbon::parse($data['fecha_entrega'] ?? now())->addMinutes($tiempoEstimado),
                'monto' => $pricing['data']['subtotal'],
                'descuento' => $pricing['data']['descuento'] ?? 0,
                'monto_final' => $pricing['data']['total'],
                'notas_cliente' => $data['instrucciones_especiales'] ?? null,
            ]);

            // Crear delivery
            $delivery = Delivery::create([
                'reserva_id' => $reservation->id,
                'tipo' => DeliveryType::Vehiculo,
                'vehiculo_id' => $vehicle->id,
                'nombre_destinatario' => $data['nombre_destinatario'],
                'telefono_destinatario' => $data['telefono_destinatario'],
                'requiere_firma' => true, // Siempre requiere firma para vehículos
                'instrucciones_especiales' => $data['instrucciones_especiales'] ?? null,
            ]);

            // Marcar vehículo como ocupado
            $vehicle->update(['estado' => 'ocupado']);

            return $reservation->load(['delivery', 'vehicle', 'user', 'branch']);
        }, 'crear_domicilio_vehiculo');
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

            $delivery->reservation->update([
                'conductor_id' => $conductorId,
                'estado' => ReservationStatus::Confirmada,
                'fecha_confirmacion' => now()
            ]);

            return $delivery->fresh()->load('reservation.driver');
        }, 'asignar_conductor');
    }

    /**
     * Iniciar domicilio (conductor recoge)
     */
    public function startDelivery(int $deliveryId): array
    {
        return $this->executeWithTransaction(function () use ($deliveryId) {
            $delivery = Delivery::findOrFail($deliveryId);

            if (!$delivery->reservation->isConfirmada()) {
                throw new Exception('Solo se pueden iniciar domicilios confirmados');
            }

            $delivery->update([
                'fecha_recogida' => now(),
            ]);

            $delivery->reservation->update([
                'estado' => ReservationStatus::Activa,
                'fecha_inicio_real' => now()
            ]);

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

            if (!$delivery->reservation->isActiva()) {
                throw new Exception('Solo se pueden completar domicilios activos');
            }

            $delivery->update([
                'fecha_entrega' => now(),
                'firma_destinatario' => $completionData['firma'] ?? null,
                'foto_entrega' => $completionData['foto'] ?? null,
                'notas_entrega' => $completionData['notas'] ?? null,
            ]);

            $delivery->reservation->update([
                'estado' => ReservationStatus::Completada,
                'fecha_fin_real' => now()
            ]);

            // Liberar vehículo si es delivery de vehículo
            if ($delivery->tipo === DeliveryType::Vehiculo && $delivery->vehicle) {
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
            return Reservation::where('user_id', $userId)
                ->where('tipo', ReservationType::Domicilio)
                ->whereIn('estado', [
                    ReservationStatus::Pendiente,
                    ReservationStatus::Confirmada,
                    ReservationStatus::Activa
                ])
                ->with(['delivery', 'driver', 'vehicle'])
                ->orderByDesc('created_at')
                ->get();
        }, 'obtener_domicilios_activos_usuario');
    }

    /**
     * Obtener domicilios pendientes de asignación
     */
    public function getPendingDeliveries(): array
    {
        return $this->execute(function () {
            return Reservation::where('tipo', ReservationType::Domicilio)
                ->where('estado', ReservationStatus::Pendiente)
                ->whereNull('conductor_id')
                ->with(['delivery', 'user', 'vehicle'])
                ->orderBy('created_at')
                ->get();
        }, 'obtener_domicilios_pendientes');
    }

    /**
     * Rastrear domicilio en tiempo real
     */
    public function trackDelivery(int $deliveryId): array
    {
        return $this->execute(function () use ($deliveryId) {
            $delivery = Delivery::with([
                'reservation.driver',
                'reservation.gpsTracks' => function ($query) {
                    $query->latest()->limit(1);
                }
            ])->findOrFail($deliveryId);

            $lastTrack = $delivery->reservation->gpsTracks->first();

            return [
                'delivery' => $delivery,
                'estado' => $delivery->reservation->estado,
                'ultima_ubicacion' => $lastTrack ? [
                    'lat' => $lastTrack->latitud,
                    'lng' => $lastTrack->longitud,
                    'velocidad' => $lastTrack->velocidad,
                    'timestamp' => $lastTrack->fecha_registro
                ] : null,
                'tiempo_transcurrido' => $delivery->fecha_recogida 
                    ? $delivery->fecha_recogida->diffInMinutes(now()) 
                    : null,
                'tiempo_estimado_restante' => $delivery->reservation->duracion_minutos
            ];
        }, 'rastrear_domicilio');
    }
}