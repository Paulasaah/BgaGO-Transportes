<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $nombre
 * @property string|null $direccion
 * @property float $lat
 * @property float $lon
 * @property int $radio Radio en metros
 * @property string $color
 * @property string|null $descripcion
 * @property int $capacidad_vehiculos
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * @property-read int|null $reservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Vehicle> $vehicles
 * @property-read int|null $vehicles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereCapacidadVehiculos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereRadio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch withoutTrashed()
 * @mixin \Eloquent
 */
class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'direccion',
        'ciudad',
        'lat',
        'lon',
        'radio',
        'color',
        'descripcion',
        'capacidad_vehiculos',
    ];

    protected $casts = [
        'lat' => 'float',
        'lon' => 'float',
        'radio' => 'integer',
        'capacidad_vehiculos' => 'integer',
    ];

    /**
     * Relación con vehículos
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'sede_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'sede_id');
    }

    /**
     * Obtener dispositivos actualmente en esta sede
     */
    public function getCurrentDevices()
    {
        return \App\Models\Telemetria::latestByDevice()
            ->where('current_branch', $this->nombre)
            ->get();
    }

    /**
     * Calcular porcentaje de ocupación
     */
    public function getOccupancyPercentage()
    {
        $currentDevices = $this->getCurrentDevices()->count();
        return $this->capacidad_vehiculos > 0 
            ? round(($currentDevices / $this->capacidad_vehiculos) * 100, 1)
            : 0;
    }
}