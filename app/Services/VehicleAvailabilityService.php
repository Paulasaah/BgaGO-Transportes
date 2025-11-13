<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\Reservation;
use App\Enums\VehicleStatus;
use App\Enums\VehicleType;
use App\Enums\ReservationStatus;
use Carbon\Carbon;

class VehicleAvailabilityService extends BaseService
{
    /**
     * Verificar disponibilidad de vehículo en rango de fechas
     */
    public function checkAvailability(Vehicle $vehicle, Carbon $fechaInicio, Carbon $fechaFin): array
    {
        return $this->execute(function () use ($vehicle, $fechaInicio, $fechaFin) {
            // Verificar estado del vehículo
            if (!$vehicle->isDisponible()) {
                throw new \Exception("El vehículo no está disponible (Estado: {$vehicle->estado->label()})");
            }

            // Verificar si está visible en catálogo
            if (!$vehicle->visible_catalogo) {
                throw new \Exception('El vehículo no está disponible para reservas');
            }

            // Verificar si hay conflictos con otras reservas
            $hasConflict = Reservation::where('vehiculo_id', $vehicle->id)
                ->whereIn('estado', [
                    ReservationStatus::Pendiente,
                    ReservationStatus::Confirmada,
                    ReservationStatus::Activa
                ])
                ->where(function ($query) use ($fechaInicio, $fechaFin) {
                    // Caso 1: Nueva reserva inicia dentro de reserva existente
                    $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                        // Caso 2: Nueva reserva termina dentro de reserva existente
                        ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                        // Caso 3: Nueva reserva envuelve completamente reserva existente
                        ->orWhere(function ($q) use ($fechaInicio, $fechaFin) {
                            $q->where('fecha_inicio', '<=', $fechaInicio)
                              ->where('fecha_fin', '>=', $fechaFin);
                        });
                })
                ->exists();

            if ($hasConflict) {
                throw new \Exception('El vehículo ya está reservado en el rango de fechas solicitado');
            }

            return [
                'available' => true,
                'vehicle' => $vehicle,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin
            ];
        }, 'verificar_disponibilidad_vehiculo');
    }

    /**
     * Obtener vehículos disponibles en rango de fechas
     */
    public function getAvailableVehicles(
        Carbon $fechaInicio,
        Carbon $fechaFin,
        ?int $sedeId = null,
        ?VehicleType $tipo = null
    ): array {
        return $this->execute(function () use ($fechaInicio, $fechaFin, $sedeId, $tipo) {
            $query = Vehicle::where('estado', VehicleStatus::Disponible)
                ->where('visible_catalogo', true);

            // Filtrar por sede
            if ($sedeId) {
                $query->where('sede_id', $sedeId);
            }

            // Filtrar por tipo
            if ($tipo) {
                $query->where('tipo', $tipo);
            }

            // Obtener vehículos base
            $vehicles = $query->get();

            // Filtrar los que no tienen conflictos
            $availableVehicles = $vehicles->filter(function ($vehicle) use ($fechaInicio, $fechaFin) {
                return $vehicle->isAvailableInRange($fechaInicio, $fechaFin);
            });

            return $availableVehicles->values();
        }, 'obtener_vehiculos_disponibles');
    }

    /**
     * Obtener vehículos por tipo y sede
     */
    public function getVehiclesByTypeAndBranch(VehicleType $tipo, int $sedeId): array
    {
        return $this->execute(function () use ($tipo, $sedeId) {
            return Vehicle::where('tipo', $tipo)
                ->where('sede_id', $sedeId)
                ->where('visible_catalogo', true)
                ->where('estado', VehicleStatus::Disponible)
                ->with(['branch', 'driverProfile'])
                ->get();
        }, 'obtener_vehiculos_tipo_sede');
    }

    /**
     * Verificar disponibilidad para domicilio
     */
    public function checkDeliveryAvailability(?int $vehiculoId = null): array
    {
        return $this->execute(function () use ($vehiculoId) {
            // Si se solicita vehículo específico
            if ($vehiculoId) {
                $vehicle = Vehicle::findOrFail($vehiculoId);
                
                if (!$vehicle->isDisponible()) {
                    throw new \Exception("El vehículo no está disponible");
                }

                return [
                    'available' => true,
                    'vehicle' => $vehicle,
                    'tipo' => 'vehiculo'
                ];
            }

            // Para domicilios de paquete, verificar disponibilidad general
            $conductoresDisponibles = $this->getAvailableDrivers();

            if ($conductoresDisponibles->isEmpty()) {
                throw new \Exception('No hay conductores disponibles en este momento');
            }

            return [
                'available' => true,
                'conductores_count' => $conductoresDisponibles->count(),
                'tipo' => 'paquete'
            ];
        }, 'verificar_disponibilidad_domicilio');
    }

    /**
     * Obtener conductores disponibles
     */
    protected function getAvailableDrivers()
    {
        // Obtener conductores que no tienen reservas activas
        return \App\Models\User::role('conductor')
            ->whereDoesntHave('conductorReservations', function ($query) {
                $query->whereIn('estado', [
                    ReservationStatus::Confirmada,
                    ReservationStatus::Activa
                ]);
            })
            ->with('driverProfile')
            ->get();
    }

    /**
     * Obtener horarios ocupados de un vehículo
     */
    public function getVehicleSchedule(int $vehiculoId, Carbon $desde, Carbon $hasta): array
    {
        return $this->execute(function () use ($vehiculoId, $desde, $hasta) {
            $reservations = Reservation::where('vehiculo_id', $vehiculoId)
                ->whereIn('estado', [
                    ReservationStatus::Pendiente,
                    ReservationStatus::Confirmada,
                    ReservationStatus::Activa
                ])
                ->whereBetween('fecha_inicio', [$desde, $hasta])
                ->orderBy('fecha_inicio')
                ->get(['id', 'codigo', 'fecha_inicio', 'fecha_fin', 'estado']);

            return [
                'vehiculo_id' => $vehiculoId,
                'periodo' => [
                    'desde' => $desde,
                    'hasta' => $hasta
                ],
                'reservas' => $reservations,
                'total_reservas' => $reservations->count()
            ];
        }, 'obtener_horario_vehiculo');
    }

    /**
     * Sugerir vehículos alternativos
     */
    public function suggestAlternatives(
        VehicleType $tipo,
        Carbon $fechaInicio,
        Carbon $fechaFin,
        ?int $sedeId = null
    ): array {
        return $this->execute(function () use ($tipo, $fechaInicio, $fechaFin, $sedeId) {
            // Obtener vehículos del mismo tipo disponibles
            $alternatives = $this->getAvailableVehicles(
                $fechaInicio,
                $fechaFin,
                $sedeId,
                $tipo
            );

            // Si no hay del mismo tipo, buscar tipos similares
            if ($alternatives['data']->isEmpty()) {
                $similarTypes = $this->getSimilarVehicleTypes($tipo);
                
                $allAlternatives = collect();
                foreach ($similarTypes as $similarType) {
                    $similar = $this->getAvailableVehicles(
                        $fechaInicio,
                        $fechaFin,
                        $sedeId,
                        $similarType
                    );
                    $allAlternatives = $allAlternatives->merge($similar['data']);
                }

                return $allAlternatives;
            }

            return $alternatives['data'];
        }, 'sugerir_alternativas');
    }

    /**
     * Obtener tipos de vehículos similares
     */
    protected function getSimilarVehicleTypes(VehicleType $tipo): array
    {
        return match($tipo) {
            VehicleType::Moto => [VehicleType::Scooter],
            VehicleType::Bicicleta => [VehicleType::Scooter, VehicleType::Patineta],
            VehicleType::Scooter => [VehicleType::Bicicleta, VehicleType::Patineta, VehicleType::Moto],
            VehicleType::Patineta => [VehicleType::Scooter, VehicleType::Bicicleta],
        };
    }

    /**
     * Calcular tasa de ocupación de un vehículo
     */
    public function getVehicleOccupancyRate(int $vehiculoId, int $days = 30): array
    {
        return $this->execute(function () use ($vehiculoId, $days) {
            $desde = now()->subDays($days);
            $hasta = now();

            $totalHours = $days * 24;

            $reservedHours = Reservation::where('vehiculo_id', $vehiculoId)
                ->where('estado', ReservationStatus::Completada)
                ->whereBetween('fecha_inicio', [$desde, $hasta])
                ->get()
                ->sum(function ($reservation) {
                    return $reservation->fecha_inicio->diffInHours($reservation->fecha_fin);
                });

            $occupancyRate = ($reservedHours / $totalHours) * 100;

            return [
                'vehiculo_id' => $vehiculoId,
                'periodo_dias' => $days,
                'horas_totales' => $totalHours,
                'horas_reservadas' => $reservedHours,
                'tasa_ocupacion' => round($occupancyRate, 2),
                'estado' => $occupancyRate > 70 ? 'alta' : ($occupancyRate > 40 ? 'media' : 'baja')
            ];
        }, 'calcular_tasa_ocupacion');
    }
}