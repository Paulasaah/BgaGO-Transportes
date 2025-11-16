<?php

namespace App\Events;

use App\Models\Telemetria;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event: Telemetría 
 * 
 * Se dispara cuando se recibe nueva telemetría vía MQTT
 * Broadcasting público para que el mapa se actualice en tiempo real
 */
class TelemetryUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Telemetria $telemetry;

    /**
     * Create a new event instance.
     */
    public function __construct(Telemetria $telemetry)
    {
        $this->telemetry = $telemetry;
    }

    /**
     * Get the channels the event should broadcast on.
     * 
     * Canal público para que todos los usuarios puedan ver el mapa en tiempo real
     */
    public function broadcastOn(): Channel
    {
        return new Channel('telemetry');
    }

    /**
     * Nombre del evento en el cliente
     */
    public function broadcastAs(): string
    {
        return 'telemetry.updated';
    }

    /**
     * Datos que se envían al cliente
     */
    public function broadcastWith(): array
    {
        return [
            'device_id' => $this->telemetry->device_id,
            'type' => $this->telemetry->device_type,
            'status' => $this->telemetry->status,
            'lat' => (float) $this->telemetry->lat,
            'lng' => (float) $this->telemetry->lon,
            'battery' => (float) $this->telemetry->battery,
            'battery_health' => (float) $this->telemetry->battery_health,
            'speed' => (float) $this->telemetry->speed,
            'current_branch' => $this->telemetry->current_branch,
            'target_branch' => $this->telemetry->target_branch,
            'odometer' => (float) $this->telemetry->odometer,
            'trip_count' => (int) $this->telemetry->trip_count,
            'maintenance_km_left' => (float) $this->telemetry->maintenance_km_left,
            'needs_maintenance' => $this->telemetry->needsMaintenance(),
            'driver_name' => $this->telemetry->driver_name,
            'deliveries_completed' => (int) $this->telemetry->deliveries_completed,
            'rating' => (float) ($this->telemetry->rating ?? 0),
            'updated_at' => $this->telemetry->updated_at->toIso8601String(),
        ];
    }
}
