<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
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
        return $this->hasMany(Vehicle::class);
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