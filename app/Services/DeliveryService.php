<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Delivery;
use App\Models\Vehicle;
use App\Enums\ReservationStatus;
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
                'tipo' => \App\Enums\ReservationType::Domicilio,
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

                // Direcciones
                'direccion_origen' => $data['direccion_origen'],
                'lat_origen' => $data['lat_origen'],
                'lon_origen' => $data['lon_origen'],
                'direccion_destino' => $data['direccion_destino'],
                'lat_destino' => $data['lat_destino'],
                'lon_destino' => $data['lon_destino'],

                // Datos de contacto
                'nombre_remitente' => $data['nombre_remitente'],
                'telefono_remitente' => $data['telefono_remitente'],
                'nombre_destinatario' => $data['nombre_destinatario'],
                'telefono_destinatario' => $data['telefono_destinatario'],

                // Detalles adicionales
                'descripcion_contenido' => $data['descripcion_contenido'] ?? null,
                'peso_estimado' => $data['peso_kg'] ?? null,
                'requiere_firma' => $data['requiere_firma'] ?? false,
                'es_fragil' => $data['es_fragil'] ?? false,
                'instrucciones_especiales' => $data['instrucciones_especiales'] ?? null,
                'costo' => $data['costo'] ?? $pricing['data']['total'] ?? 0,
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
                'estado' => 'en_camino', // cambia de 'pendiente' a 'en_camino' al asignar
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

            // 👑 Admin puede iniciar cualquier entrega
            if ($user && $user->hasRole(['admin', 'super_admin'])) {
                $delivery->update([
                    'estado' => 'en_camino',
                    'fecha_recogida' => now(),
                ]);
                return $delivery->fresh();
            }

            // 🚗 Estados válidos para iniciar
            $permitidos = ['pendiente', 'asignada', 'confirmada'];
            if (!in_array($delivery->estado, $permitidos)) {
                throw new Exception('Solo se pueden iniciar domicilios confirmados o asignados');
            }

            $delivery->update([
                'estado' => 'en_camino',
                'fecha_recogida' => now(),
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
