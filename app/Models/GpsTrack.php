<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property int $vehiculo_id
 * @property int|null $reserva_id
 * @property float $latitud Latitud del vehículo
 * @property float $longitud Longitud del vehículo
 * @property float|null $altitud Altitud en metros
 * @property float|null $precision Precisión del GPS en metros
 * @property float|null $velocidad Velocidad en km/h
 * @property bool $motor_encendido Estado del motor: encendido/apagado
 * @property int|null $nivel_bateria Porcentaje de batería (0-100)
 * @property int|null $kilometraje Kilometraje registrado en este punto
 * @property float|null $temperatura_motor Temperatura del motor en °C
 * @property string $fuente Origen de la información
 * @property \Illuminate\Support\Carbon $fecha_registro Fecha y hora del registro del GPS
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Reservation|null $reservation
 * @property-read \App\Models\Vehicle $vehicle
 * @method static Builder<static>|GpsTrack deLaReserva(int $reservaId)
 * @method static Builder<static>|GpsTrack delVehiculo(int $vehiculoId)
 * @method static Builder<static>|GpsTrack enRango($desde, $hasta)
 * @method static Builder<static>|GpsTrack newModelQuery()
 * @method static Builder<static>|GpsTrack newQuery()
 * @method static Builder<static>|GpsTrack query()
 * @method static Builder<static>|GpsTrack recientes(int $limit = 10)
 * @method static Builder<static>|GpsTrack ultimoPunto()
 * @method static Builder<static>|GpsTrack whereAltitud($value)
 * @method static Builder<static>|GpsTrack whereCreatedAt($value)
 * @method static Builder<static>|GpsTrack whereFechaRegistro($value)
 * @method static Builder<static>|GpsTrack whereFuente($value)
 * @method static Builder<static>|GpsTrack whereId($value)
 * @method static Builder<static>|GpsTrack whereKilometraje($value)
 * @method static Builder<static>|GpsTrack whereLatitud($value)
 * @method static Builder<static>|GpsTrack whereLongitud($value)
 * @method static Builder<static>|GpsTrack whereMotorEncendido($value)
 * @method static Builder<static>|GpsTrack whereNivelBateria($value)
 * @method static Builder<static>|GpsTrack wherePrecision($value)
 * @method static Builder<static>|GpsTrack whereReservaId($value)
 * @method static Builder<static>|GpsTrack whereTemperaturaMotor($value)
 * @method static Builder<static>|GpsTrack whereUpdatedAt($value)
 * @method static Builder<static>|GpsTrack whereVehiculoId($value)
 * @method static Builder<static>|GpsTrack whereVelocidad($value)
 * @mixin \Eloquent
 */
class GpsTrack extends Model
{
    use HasFactory;

    protected $fillable = [
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

    // ==========================================
    // RELACIONES
    // ==========================================

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehiculo_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reserva_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

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

    // ==========================================
    // HELPER METHODS
    // ==========================================

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
