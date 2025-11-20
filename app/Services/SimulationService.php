<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
use App\Enums\VehicleStatus;
use App\Models\Reservation;
use App\Models\Vehicle;

/**
 * Servicio para exponer datos de simulación IoT al publisher externo.
 * No realiza simulación ni escritura de telemetría, solo prepara DTOs.
 */
class SimulationService extends BaseService
{
    /**
     * Obtener servicios activos (reservas en curso) listos para simulación.
     * Incluye vehículo, sede y ruta OSRM en GeoJSON.
     */
    public function getActiveServicesForSimulation(): array
    {
        return $this->execute(function () {
            $reservations = Reservation::with(['vehicle', 'branch', 'user', 'driver'])
                ->where('estado', ReservationStatus::Activa)
                ->whereNotNull('vehiculo_id')
                ->orderByDesc('fecha_inicio')
                ->limit(200)
                ->get();

            return $reservations->map(function (Reservation $reservation) {
                $vehicle = $reservation->vehicle;
                $branch = $reservation->branch;

                return [
                    'id' => $reservation->id,
                    'code' => $reservation->codigo,
                    'type' => $reservation->tipo instanceof ReservationType
                        ? $reservation->tipo->value
                        : (string) $reservation->tipo,
                    'state' => $reservation->estado instanceof ReservationStatus
                        ? $reservation->estado->value
                        : (string) $reservation->estado,
                    'user' => [
                        'id' => $reservation->user?->id,
                        'name' => $reservation->user?->name,
                    ],
                    'driver' => $reservation->driver ? [
                        'id' => $reservation->driver->id,
                        'name' => $reservation->driver->name,
                    ] : null,
                    'vehicle' => $vehicle ? [
                        'id' => $vehicle->id,
                        'placa' => $vehicle->placa,
                        // Por ahora usamos la placa como device_id lógico del vehículo
                        'device_id' => $vehicle->placa,
                        'tipo' => $vehicle->tipo?->value ?? null,
                    ] : null,
                    'route' => [
                        'origin' => [
                            'lat' => $reservation->origen_lat,
                            'lng' => $reservation->origen_lng,
                            'address' => $reservation->origen_direccion,
                        ],
                        'destination' => [
                            'lat' => $reservation->destino_lat,
                            'lng' => $reservation->destino_lng,
                            'address' => $reservation->destino_direccion,
                        ],
                        'distance_km' => $reservation->distancia_km !== null
                            ? (float) $reservation->distancia_km
                            : null,
                        'duration_minutes' => $reservation->duracion_minutos !== null
                            ? (int) $reservation->duracion_minutos
                            : null,
                        // GeoJSON devuelto por OSRM y almacenado en waypoints
                        'geometry' => $reservation->waypoints,
                    ],
                    'time' => [
                        'start_planned' => $reservation->fecha_inicio?->toIso8601String(),
                        'end_planned' => $reservation->fecha_fin?->toIso8601String(),
                        'start_real' => $reservation->fecha_inicio_real?->toIso8601String(),
                        'end_real' => $reservation->fecha_fin_real?->toIso8601String(),
                    ],
                    'branch' => $branch ? [
                        'id' => $branch->id,
                        'name' => $branch->nombre,
                        'lat' => $branch->lat,
                        'lng' => $branch->lon,
                    ] : null,
                ];
            })->toArray();
        }, 'simulation_active_services');
    }

    /**
     * Obtener vehículos sin servicio activo (idle) para simulación.
     * Se usan las coordenadas de la sede actual como posición base.
     */
    public function getIdleVehiclesForSimulation(): array
    {
        if (!config('simulation.simulate_idle_vehicles', true)) {
            return [];
        }

        return $this->execute(function () {
            $vehicles = Vehicle::with('branch')
                ->where('estado', VehicleStatus::Disponible)
                ->whereDoesntHave('reservations', function ($q) {
                    $q->whereIn('estado', [
                        ReservationStatus::Confirmada->value,
                        ReservationStatus::Activa->value,
                    ]);
                })
                ->orderBy('id')
                ->limit(200)
                ->get();

            return $vehicles->map(function (Vehicle $vehicle) {
                $branch = $vehicle->branch;

                return [
                    'id' => $vehicle->id,
                    'placa' => $vehicle->placa,
                    // Igual que en servicios activos, device_id lógico = placa
                    'device_id' => $vehicle->placa,
                    'status' => $vehicle->estado instanceof VehicleStatus
                        ? $vehicle->estado->value
                        : (string) $vehicle->estado,
                    'type' => $vehicle->tipo?->value ?? null,
                    'branch' => $branch ? [
                        'id' => $branch->id,
                        'name' => $branch->nombre,
                        'lat' => $branch->lat,
                        'lng' => $branch->lon,
                    ] : null,
                ];
            })->toArray();
        }, 'simulation_idle_vehicles');
    }
}
