<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Vehicle;
use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
use Carbon\Carbon;
use Exception;

class ReservationService extends BaseService
{
    protected PricingService $pricingService;
    protected VehicleAvailabilityService $availabilityService;

    public function __construct(
        PricingService $pricingService,
        VehicleAvailabilityService $availabilityService
    ) {
        $this->pricingService = $pricingService;
        $this->availabilityService = $availabilityService;
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

            $total = ($pricing['data']['total'] ?? 0)
                + ((isset($data['entrega_domicilio']) && $data['entrega_domicilio']) ? 5000 : 0);

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
                'monto_final' => $total,
                'duracion_minutos' => $fechaInicio->diffInMinutes($fechaFin),
                'notas_cliente' => $data['notas_cliente'] ?? null,
                'origen_direccion' => $data['origen_direccion'] ?? 'Sin dirección',
                'destino_direccion' => $data['destino_direccion'] ?? null,
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

            // Liberar vehículo
            $reservation->vehicle->update(['estado' => 'disponible']);

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

            $user = auth()->user();
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
}