<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Vehicle;
use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class ReservationService extends BaseService
{
    protected PricingService $pricingService;
    protected VehicleAvailabilityService $availabilityService;
    protected RouteService $routeService;

    public function __construct(
        PricingService $pricingService,
        VehicleAvailabilityService $availabilityService,
        RouteService $routeService
    ) {
        $this->pricingService = $pricingService;
        $this->availabilityService = $availabilityService;
        $this->routeService = $routeService;
    }

    /**
     * Crear nueva reserva de vehículo
     */
    public function createReservation(array $data): array
    {
        return $this->executeWithTransaction(function () use ($data) {
            // Validar campos requeridos
            $this->validateRequired($data, [
                'user_id',
                'vehiculo_id',
                'sede_id',
                'fecha_inicio',
                'fecha_fin',
                'origen_direccion',
            ]);

            // Verificar disponibilidad
            $vehicle = Vehicle::findOrFail($data['vehiculo_id']);
            $fechaInicio = Carbon::parse($data['fecha_inicio']);
            $fechaFin = Carbon::parse($data['fecha_fin']);

            $isAvailable = $this->availabilityService->checkAvailability(
                $vehicle,
                $fechaInicio,
                $fechaFin
            );

            if (!$isAvailable['success']) {
                throw new Exception($isAvailable['message']);
            }

            $pricing = $this->pricingService->calculateReservationPrice(
                $vehicle,
                $fechaInicio,
                $fechaFin
            );

            $route = null;
            $deliveryDistanceKm = null;
            $deliveryDurationMin = null;

            $isDomicilio = !empty($data['entrega_domicilio']);
            $destLat = $data['destino_lat'] ?? $data['lat_recogida'] ?? null;
            $destLng = $data['destino_lng'] ?? $data['lon_recogida'] ?? null;

            if ($isDomicilio && $vehicle->branch && $destLat !== null && $destLng !== null) {
                $branchLat = (float) ($vehicle->branch->lat ?? 0);
                $branchLng = (float) ($vehicle->branch->lon ?? 0);

                if ($branchLat && $branchLng) {
                    $routeResult = $this->routeService->calculateRoute(
                        $branchLat,
                        $branchLng,
                        (float) $destLat,
                        (float) $destLng
                    );

                    if ($routeResult['success']) {
                        $route = $routeResult['data'];
                        $deliveryDistanceKm = $route['distance_km'] ?? null;
                        $deliveryDurationMin = $route['duration_minutes'] ?? null;
                    } else {
                        $deliveryDistanceKm = $this->calculateDistance($branchLat, $branchLng, (float) $destLat, (float) $destLng);
                        $deliveryDurationMin = $this->calculateEstimatedTime($deliveryDistanceKm);
                    }
                }
            }

            // Crear reserva
            $reservation = Reservation::create([
                'codigo' => Reservation::generateCode(),
                'user_id' => $data['user_id'],
                'vehiculo_id' => $data['vehiculo_id'],
                'sede_id' => $data['sede_id'],
                'tipo' => ReservationType::Reserva,
                'estado' => ReservationStatus::Pendiente,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'monto' => $pricing['data']['subtotal'],
                'descuento' => $pricing['data']['descuento'] ?? 0,
                'monto_final' => $pricing['data']['total'],
                'duracion_minutos' => $deliveryDurationMin ?? $fechaInicio->diffInMinutes($fechaFin),
                'notas_cliente' => $data['notas_cliente'] ?? null,
                'origen_direccion' => $data['origen_direccion'] ?? 'Sin dirección',
                'origen_lat' => $isDomicilio ? ($vehicle->branch?->lat ?? null) : ($data['origen_lat'] ?? null),
                'origen_lng' => $isDomicilio ? ($vehicle->branch?->lon ?? null) : ($data['origen_lng'] ?? null),
                'destino_direccion' => $data['destino_direccion'] ?? null,
                'destino_lat' => $isDomicilio ? ($destLat !== null ? (float) $destLat : null) : ($data['destino_lat'] ?? null),
                'destino_lng' => $isDomicilio ? ($destLng !== null ? (float) $destLng : null) : ($data['destino_lng'] ?? null),
                'waypoints' => $route['geometry'] ?? null,
                'distancia_km' => $deliveryDistanceKm ?? ($route['distance_km'] ?? null),
            ]);

            // Actualizar estado del vehículo
            $vehicle->update(['estado' => 'ocupado']);

            return $reservation->load(['vehicle', 'user', 'branch']);
        }, 'crear_reserva');
    }

    /**
     * Confirmar reserva (después de pago aprobado)
     */
    public function confirmReservation(int $reservationId): array
    {
        return $this->executeWithTransaction(function () use ($reservationId) {
            $reservation = Reservation::findOrFail($reservationId);

            if (!$reservation->isPendiente()) {
                throw new Exception('Solo se pueden confirmar reservas pendientes');
            }

            if (!$reservation->hasPaidPayment()) {
                throw new Exception('La reserva debe tener un pago aprobado');
            }

            $reservation->update([
                'estado' => ReservationStatus::Confirmada,
                'fecha_confirmacion' => now()
            ]);

            return $reservation->fresh();
        }, 'confirmar_reserva');
    }

    /**
     * Iniciar uso del vehículo
     */
    public function startReservation(int $reservationId, ?int $conductorId = null): array
    {
        return $this->executeWithTransaction(function () use ($reservationId, $conductorId) {
            $reservation = Reservation::findOrFail($reservationId);

            if (!$reservation->isConfirmada()) {
                throw new Exception('Solo se pueden iniciar reservas confirmadas');
            }

            $updateData = [
                'estado' => ReservationStatus::Activa,
                'fecha_inicio_real' => now()
            ];

            if ($conductorId) {
                $updateData['conductor_id'] = $conductorId;
            }

            $reservation->update($updateData);

            return $reservation->fresh();
        }, 'iniciar_reserva');
    }

    /**
     * Completar reserva
     */
    public function completeReservation(int $reservationId, ?array $evaluationData = null): array
    {
        return $this->executeWithTransaction(function () use ($reservationId, $evaluationData) {
            $reservation = Reservation::findOrFail($reservationId);

            if (!$reservation->isActiva()) {
                throw new Exception('Solo se pueden completar reservas activas');
            }

            $updateData = [
                'estado' => ReservationStatus::Completada,
                'fecha_fin_real' => now()
            ];

            // Agregar evaluación si existe
            if ($evaluationData) {
                if (isset($evaluationData['calificacion_conductor'])) {
                    $updateData['calificacion_conductor'] = $evaluationData['calificacion_conductor'];
                    $updateData['comentario_conductor'] = $evaluationData['comentario_conductor'] ?? null;
                }
            }

            $reservation->update($updateData);

            if ($reservation->vehicle) {
                $reservation->vehicle->update(['estado' => 'disponible']);
            }

            return $reservation->fresh();
        }, 'completar_reserva');
    }

    /**
     * Cancelar reserva
     *
     * @param int $reservationId
     * @param string|null $motivo
     * @param int|null $canceladoPor
     * @return array
     */
    public function cancelReservation(int $reservationId, ?string $motivo = null, ?int $canceladoPor = null): array
    {
        return $this->executeWithTransaction(function () use ($reservationId, $motivo, $canceladoPor) {
            $reservation = Reservation::findOrFail($reservationId);

            // ⚠️ NOTA: Este método NO debería ser llamado por admins
            // Los admins cancelan directamente desde el Controller
            // Este método solo se llama para usuarios normales

            // 👤 USUARIOS NORMALES: Solo pueden cancelar si el estado lo permite
            if (!$reservation->canBeCancelled()) {
                throw new Exception(
                    "Esta reserva no puede ser cancelada en su estado actual ({$reservation->estado->label()})."
                );
            }

            $user = Auth::user();
            $motivoFinal = $motivo ?: 'Cancelación sin motivo especificado';

            $reservation->update([
                'estado' => ReservationStatus::Cancelada,
                'motivo_cancelacion' => $motivoFinal,
                'cancelado_por' => $canceladoPor ?? $user?->id,
                'fecha_cancelacion' => now(),
            ]);

            // Liberar vehículo si está ocupado
            if ($reservation->vehicle && $reservation->vehicle->isOcupado()) {
                $reservation->vehicle->update(['estado' => 'disponible']);
            }

            return $reservation->fresh();
        }, 'cancelar_reserva');
    }

    /**
     * Obtener reservas activas de un usuario
     */
    public function getUserActiveReservations(int $userId): array
    {
        return $this->execute(function () use ($userId) {
            return Reservation::where('user_id', $userId)
                ->whereIn('estado', [
                    ReservationStatus::Pendiente,
                    ReservationStatus::Confirmada,
                    ReservationStatus::Activa
                ])
                ->with(['vehicle', 'branch', 'driver'])
                ->orderByDesc('fecha_inicio')
                ->get();
        }, 'obtener_reservas_activas_usuario');
    }

    /**
     * Obtener historial de reservas
     */
    public function getUserReservationHistory(int $userId, int $limit = 10): array
    {
        return $this->execute(function () use ($userId, $limit) {
            return Reservation::where('user_id', $userId)
                ->whereIn('estado', [
                    ReservationStatus::Completada,
                    ReservationStatus::Cancelada
                ])
                ->with(['vehicle', 'branch'])
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();
        }, 'obtener_historial_reservas');
    }

    /**
     * Verificar si usuario tiene reservas activas
     */
    public function hasActiveReservations(int $userId): bool
    {
        return Reservation::where('user_id', $userId)
            ->whereIn('estado', [
                ReservationStatus::Confirmada,
                ReservationStatus::Activa
            ])
            ->exists();
    }

    /**
     * Obtener estadísticas de reservas del usuario
     */
    public function getUserStats(int $userId): array
    {
        return $this->execute(function () use ($userId) {
            $reservations = Reservation::where('user_id', $userId)->get();

            return [
                'total' => $reservations->count(),
                'completadas' => $reservations->where('estado', ReservationStatus::Completada)->count(),
                'canceladas' => $reservations->where('estado', ReservationStatus::Cancelada)->count(),
                'activas' => $reservations->whereIn('estado', [
                    ReservationStatus::Pendiente,
                    ReservationStatus::Confirmada,
                    ReservationStatus::Activa
                ])->count(),
                'gasto_total' => $reservations
                    ->where('estado', ReservationStatus::Completada)
                    ->sum('monto_final'),
                'promedio_calificacion' => $reservations
                    ->where('estado', ReservationStatus::Completada)
                    ->whereNotNull('calificacion_conductor')
                    ->avg('calificacion_conductor')
            ];
        }, 'obtener_estadisticas_usuario');
    }

    /**
     * Aceptación por conductor: asignar y confirmar
     */
    public function driverAccept(int $reservationId, int $driverId): array
    {
        return $this->executeWithTransaction(function () use ($reservationId, $driverId) {
            $reservation = Reservation::lockForUpdate()->findOrFail($reservationId);

            if ($reservation->estado->isFinal()) {
                throw new Exception('La reserva está en estado final');
            }

            if ($reservation->conductor_id && $reservation->conductor_id !== $driverId) {
                throw new Exception('La reserva ya tiene otro conductor asignado');
            }

            $reservation->update([
                'conductor_id' => $driverId,
                'estado' => ReservationStatus::Confirmada,
                'fecha_confirmacion' => now(),
            ]);

            $this->logSuccess('conductor_acepta_reserva', [
                'reservation_id' => $reservationId,
                'driver_id' => $driverId,
            ]);

            return $reservation->fresh();
        }, 'aceptar_reserva_conductor');
    }

    /**
     * Rechazo por conductor: cancelar pendiente/confirmada
     */
    public function driverReject(int $reservationId, int $driverId, ?string $motivo = null): array
    {
        return $this->executeWithTransaction(function () use ($reservationId, $driverId, $motivo) {
            $reservation = Reservation::lockForUpdate()->findOrFail($reservationId);

            if ($reservation->estado->isFinal()) {
                throw new Exception('La reserva está en estado final');
            }

            $reservation->update([
                'estado' => ReservationStatus::Cancelada,
                'motivo_cancelacion' => $motivo ?: 'Rechazada por el conductor',
                'cancelado_por' => $driverId,
                'fecha_cancelacion' => now(),
            ]);

            // Liberar vehículo si corresponde
            if ($reservation->vehicle && $reservation->vehicle->isOcupado()) {
                $reservation->vehicle->update(['estado' => 'disponible']);
            }

            $this->logSuccess('conductor_rechaza_reserva', [
                'reservation_id' => $reservationId,
                'driver_id' => $driverId,
            ]);

            return $reservation->fresh();
        }, 'rechazar_reserva_conductor');
    }
}
