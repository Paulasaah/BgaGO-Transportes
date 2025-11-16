<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class GpsTrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'device_type',
        'vehiculo_id',
        'reserva_id',
        'latitud',
        'longitud',
        'altitud',
        'precision',
        'velocidad',
        'motor_encendido',
        'nivel_bateria',
        'kilometraje',
        'temperatura_motor',
        'fuente',
        'fecha_registro',
    ];

    protected $casts = [
        'latitud' => 'float',
        'longitud' => 'float',
        'altitud' => 'float',
        'precision' => 'float',
        'velocidad' => 'float',
        'temperatura_motor' => 'float',
        'motor_encendido' => 'boolean',
        'nivel_bateria' => 'integer',
        'kilometraje' => 'integer',
        'fecha_registro' => 'datetime',
    ];

    // RELACIONES

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehiculo_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reserva_id');
    }

    // SCOPES

    public function scopeDelVehiculo(Builder $query, int $vehiculoId): Builder
    {
        return $query->where('vehiculo_id', $vehiculoId);
    }

    public function scopeDeLaReserva(Builder $query, int $reservaId): Builder
    {
        return $query->where('reserva_id', $reservaId);
    }

    public function scopeEnRango(Builder $query, $desde, $hasta): Builder
    {
        return $query->whereBetween('fecha_registro', [$desde, $hasta]);
    }

    public function scopeRecientes(Builder $query, int $limit = 10): Builder
    {
        return $query->orderByDesc('fecha_registro')->limit($limit);
    }

    public function scopeUltimoPunto(Builder $query): Builder
    {
        return $query->orderByDesc('fecha_registro')->limit(1);
    }

    // HELPER METHODS

    /**
     * Calcular distancia a otro punto GPS (km)
     */
    public function distanceTo(float $lat, float $lon): float
    {
        $earthRadius = 6371; // km

        $latFrom = deg2rad($this->latitud);
        $lonFrom = deg2rad($this->longitud);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lon);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Verificar si el motor está encendido
     */
    public function isMotorOn(): bool
    {
        return $this->motor_encendido;
    }

    /**
     * Verificar si la batería está baja
     */
    public function isBatteryLow(): bool
    {
        return $this->nivel_bateria !== null && $this->nivel_bateria < 20;
    }

    /**
     * Verificar si está en movimiento
     */
    public function isMoving(): bool
    {
        return $this->velocidad !== null && $this->velocidad > 5;
    }
}