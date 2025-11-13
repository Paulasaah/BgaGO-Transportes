<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $device_id
 * @property string $device_type
 * @property string $status
 * @property float $lat
 * @property float $lon
 * @property float|null $alt
 * @property float $battery
 * @property float|null $speed km/h
 * @property string|null $current_branch Sede actual
 * @property string|null $target_branch Destino
 * @property float $odometer Kilómetros totales recorridos
 * @property int $trip_count Número de viajes realizados
 * @property float $battery_health Salud de batería %
 * @property \Illuminate\Support\Carbon|null $last_maintenance
 * @property float $maintenance_km_left KM hasta próximo mantenimiento
 * @property string|null $driver_name
 * @property int $deliveries_completed
 * @property float|null $rating Calificación promedio 1-5
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria latestByDevice()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria needsMaintenance()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria recentRoute($deviceId, $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereAlt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereBattery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereBatteryHealth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereCurrentBranch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereDeliveriesCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereDriverName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereLastMaintenance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereMaintenanceKmLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereTargetBranch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereTripCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Telemetria whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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