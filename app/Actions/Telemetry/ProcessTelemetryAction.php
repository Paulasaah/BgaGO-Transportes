<?php

namespace App\Actions\Telemetry;

use App\Models\Telemetria;
use App\Models\GpsTrack;
use App\Models\Vehicle;
use App\Events\TelemetryUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Action: Procesar telemetría recibida vía MQTT
 * 
 * Responsabilidad única: Guardar telemetría en BD y disparar eventos
 * Sigue principio SOLID: Single Responsibility
 */
class ProcessTelemetryAction
{
    /**
     * Ejecutar el procesamiento de telemetría
     * 
     * @param array $data Datos de telemetría parseados del JSON MQTT
     * @return Telemetria|null
     */
    public function execute(array $data): ?Telemetria
    {
        try {
            DB::beginTransaction();

            // 1. Guardar/actualizar en tabla telemetrias (última posición)
            $telemetry = $this->saveTelemetry($data);

            // 2. Guardar en histórico gps_tracks
            $this->saveGpsTrack($data, $telemetry);

            // 3. Disparar evento para broadcasting
            broadcast(new TelemetryUpdated($telemetry))->toOthers();

            DB::commit();

            return $telemetry;

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error procesando telemetría', [
                'device_id' => $data['device_id'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Guardar/actualizar telemetría (última posición)
     */
    private function saveTelemetry(array $data): Telemetria
    {
        $telemetryData = [
            'device_id' => $data['device_id'],
            'device_type' => $data['device_type'] ?? 'vehiculo',
            'status' => $data['status'] ?? 'idle',
            'lat' => $data['Geopoint']['lat'] ?? 0,
            'lon' => $data['Geopoint']['lon'] ?? 0,
            'alt' => $data['Geopoint']['alt'] ?? null,
            'battery' => $data['Battery'] ?? 100,
            'speed' => $data['speed'] ?? 0,
            'current_branch' => $data['current_branch'] ?? null,
            'target_branch' => $data['target_branch'] ?? null,
            'odometer' => $data['odometer'] ?? 0,
            'trip_count' => $data['trip_count'] ?? 0,
            'battery_health' => $data['battery_health'] ?? 100,
            'maintenance_km_left' => $data['maintenance_km_left'] ?? 1000,
            'last_maintenance' => isset($data['last_maintenance']) 
                ? \Carbon\Carbon::parse($data['last_maintenance']) 
                : null,
        ];

        // Campos específicos para conductores
        if ($data['device_type'] === 'conductor') {
            $telemetryData['driver_name'] = $data['driver_name'] ?? null;
            $telemetryData['deliveries_completed'] = $data['deliveries_completed'] ?? 0;
            $telemetryData['rating'] = $data['rating'] ?? null;
        }

        // Usar updateOrCreate para deduplicación automática
        return Telemetria::updateOrCreate(
            ['device_id' => $data['device_id']],
            $telemetryData
        );
    }

    /**
     * Guardar en histórico GPS (todos los registros)
     */
    private function saveGpsTrack(array $data, Telemetria $telemetry): void
    {
        // Buscar vehículo asociado si existe
        $vehiculoId = null;
        $reservaId = null;

        if ($data['device_type'] === 'vehiculo') {
            $vehicle = Vehicle::where('placa', $data['device_id'])->first();
            if ($vehicle) {
                $vehiculoId = $vehicle->id;
                // Buscar reserva activa si existe
                $activeReservation = $vehicle->getActiveReservation()->first();
                $reservaId = $activeReservation?->id;
            }
        }

        GpsTrack::create([
            'device_id' => $data['device_id'],
            'device_type' => $data['device_type'] ?? 'vehiculo',
            'vehiculo_id' => $vehiculoId,
            'reserva_id' => $reservaId,
            'latitud' => $data['Geopoint']['lat'] ?? 0,
            'longitud' => $data['Geopoint']['lon'] ?? 0,
            'altitud' => $data['Geopoint']['alt'] ?? null,
            'velocidad' => $data['speed'] ?? 0,
            'motor_encendido' => $data['status'] === 'active',
            'nivel_bateria' => (int) ($data['Battery'] ?? 100),
            'kilometraje' => (int) ($data['odometer'] ?? 0),
            'fuente' => 'gps',
            'fecha_registro' => now(),
        ]);
    }
}
