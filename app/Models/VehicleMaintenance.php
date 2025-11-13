<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $vehiculo_id
 * @property int|null $realizado_por
 * @property string $tipo Tipo de mantenimiento realizado
 * @property string $estado Estado actual del mantenimiento
 * @property int|null $kilometraje_actual Kilometraje al momento del mantenimiento
 * @property int|null $kilometraje_proximo Kilometraje estimado para el próximo mantenimiento
 * @property int|null $kilometraje_intervalo Intervalo recomendado entre mantenimientos en km
 * @property \Illuminate\Support\Carbon|null $fecha_programada
 * @property \Illuminate\Support\Carbon|null $fecha_realizada
 * @property float $costo Costo total del mantenimiento
 * @property string $descripcion Descripción general del mantenimiento realizado
 * @property string|null $repuestos_usados Lista de repuestos utilizados
 * @property string|null $taller Nombre del taller o proveedor
 * @property string|null $mecanico Nombre del mecánico encargado
 * @property string|null $observaciones Observaciones adicionales
 * @property array<array-key, mixed>|null $archivos Archivos adjuntos del mantenimiento
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereArchivos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereCosto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereFechaProgramada($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereFechaRealizada($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereKilometrajeActual($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereKilometrajeIntervalo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereKilometrajeProximo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereMecanico($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereObservaciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereRealizadoPor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereRepuestosUsados($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereTaller($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance whereVehiculoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleMaintenance withoutTrashed()
 * @mixin \Eloquent
 */
class VehicleMaintenance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vehicle_maintenances';

    protected $fillable = [
        'vehiculo_id',
        'realizado_por',
        'tipo',
        'estado',
        'kilometraje_actual',
        'kilometraje_proximo',
        'kilometraje_intervalo',
        'fecha_programada',
        'fecha_realizada',
        'costo',
        'descripcion',
        'repuestos_usados',
        'taller',
        'mecanico',
        'observaciones',
        'archivos',
    ];

    protected $casts = [
        'costo' => 'float',
        'fecha_programada' => 'date',
        'fecha_realizada' => 'date',
        'archivos' => 'array',
    ];

    // Relaciones
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehiculo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'realizado_por');
    }
}
