<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Telemetria extends Model
{
    use HasFactory;

    protected $table = 'telemetrias';

    protected $fillable = [
        'device_id',
        'device_type',
        'status',
        'lat',
        'lon',
        'alt',
        'battery',
        'speed',
        'current_branch',
        'target_branch',
        'odometer',
        'trip_count',
        'battery_health',
        'last_maintenance',
        'maintenance_km_left',
        'driver_name',
        'deliveries_completed',
        'rating',
    ];

    protected $casts = [
        'battery' => 'float',
        'speed' => 'float',
        'odometer' => 'float',
        'battery_health' => 'float',
        'rating' => 'float',
        'last_maintenance' => 'datetime',
    ];

    /**
     * Último registro por cada device_id
     */
    public function scopeLatestByDevice($query)
    {
        return $query->select('telemetrias.*')
            ->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('telemetrias')
                    ->groupBy('device_id');
            });
    }

    /**
     * Filtrar por tipo de dispositivo
     */
    public function scopeByType($query, $type)
    {
        return $query->where('device_type', $type);
    }

    /**
     * Dispositivos que necesitan mantenimiento
     */
    public function scopeNeedsMaintenance($query)
    {
        return $query->where('maintenance_km_left', '<=', 100)
                    ->orWhere('battery_health', '<=', 70);
    }

    /**
     * Historial reciente (últimos N puntos) para una ruta
     */
    public function scopeRecentRoute($query, $deviceId, $limit = 10)
    {
        return $query->where('device_id', $deviceId)
                     ->orderByDesc('id')
                     ->take($limit)
                     ->get(['lat', 'lon', 'speed']);
    }

    /**
     * Verificar si necesita mantenimiento
     */
    public function needsMaintenance(): bool
    {
        return $this->maintenance_km_left <= 100 || $this->battery_health <= 70;
    }

    /**
     * Obtener estado del dispositivo
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'active' => 'Activo',
            'idle' => 'En espera',
            'charging' => 'Cargando',
            'maintenance' => 'Mantenimiento',
            'offline' => 'Desconectado',
            default => 'Desconocido'
        };
    }
}