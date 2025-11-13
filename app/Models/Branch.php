<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'direccion',
        'ciudad',
        'telefono',
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
