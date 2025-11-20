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

    /**
     * Encontrar la sede más cercana a unas coordenadas dadas.
     */
    public static function findNearestTo(float $lat, float $lon): ?self
    {
        $branches = self::whereNotNull('lat')
            ->whereNotNull('lon')
            ->get();

        if ($branches->isEmpty()) {
            return null;
        }

        $deg2rad = M_PI / 180;
        $best = null;

        foreach ($branches as $branch) {
            $dLat = ($branch->lat - $lat) * $deg2rad;
            $dLon = ($branch->lon - $lon) * $deg2rad;

            $a = sin($dLat / 2) ** 2
                + cos($lat * $deg2rad) * cos($branch->lat * $deg2rad)
                * sin($dLon / 2) ** 2;

            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distance = 6371 * $c; // km

            if ($best === null || $distance < $best['distance']) {
                $best = [
                    'branch' => $branch,
                    'distance' => $distance,
                ];
            }
        }

        return $best['branch'] ?? null;
    }
}