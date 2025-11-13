<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $codigo
 * @property int $user_id
 * @property int|null $vehiculo_id
 * @property int|null $conductor_id
 * @property int $sede_id
 * @property ReservationType $tipo
 * @property ReservationStatus $estado
 * @property string $origen_direccion
 * @property float|null $origen_lat
 * @property float|null $origen_lng
 * @property string $destino_direccion
 * @property float|null $destino_lat
 * @property float|null $destino_lng
 * @property \Illuminate\Support\Carbon $fecha_inicio
 * @property \Illuminate\Support\Carbon $fecha_fin
 * @property \Illuminate\Support\Carbon|null $fecha_confirmacion
 * @property \Illuminate\Support\Carbon|null $fecha_inicio_real
 * @property \Illuminate\Support\Carbon|null $fecha_fin_real
 * @property numeric $monto
 * @property numeric $descuento
 * @property numeric $monto_final
 * @property string|null $notas_cliente
 * @property string|null $notas_conductor
 * @property string|null $notas_admin
 * @property array<array-key, mixed>|null $waypoints
 * @property int|null $distancia_km
 * @property int|null $duracion_minutos
 * @property int|null $calificacion_conductor
 * @property string|null $comentario_conductor
 * @property int|null $calificacion_cliente
 * @property string|null $comentario_cliente
 * @property string|null $motivo_cancelacion
 * @property int|null $cancelado_por
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\User|null $cancelledBy
 * @property-read \App\Models\Delivery|null $delivery
 * @property-read \App\Models\User|null $driver
 * @property-read \App\Models\DriverProfile|null $driverProfile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GpsTrack> $gpsTracks
 * @property-read int|null $gps_tracks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Vehicle|null $vehicle
 * @method static Builder<static>|Reservation activas()
 * @method static Builder<static>|Reservation canceladas()
 * @method static Builder<static>|Reservation completadas()
 * @method static Builder<static>|Reservation confirmadas()
 * @method static Builder<static>|Reservation delConductor(int $conductorId)
 * @method static Builder<static>|Reservation delUsuario(int $userId)
 * @method static Builder<static>|Reservation domicilios()
 * @method static Builder<static>|Reservation enRango($desde, $hasta)
 * @method static Builder<static>|Reservation newModelQuery()
 * @method static Builder<static>|Reservation newQuery()
 * @method static Builder<static>|Reservation onlyTrashed()
 * @method static Builder<static>|Reservation pendientes()
 * @method static Builder<static>|Reservation query()
 * @method static Builder<static>|Reservation reservas()
 * @method static Builder<static>|Reservation whereCalificacionCliente($value)
 * @method static Builder<static>|Reservation whereCalificacionConductor($value)
 * @method static Builder<static>|Reservation whereCanceladoPor($value)
 * @method static Builder<static>|Reservation whereCodigo($value)
 * @method static Builder<static>|Reservation whereComentarioCliente($value)
 * @method static Builder<static>|Reservation whereComentarioConductor($value)
 * @method static Builder<static>|Reservation whereConductorId($value)
 * @method static Builder<static>|Reservation whereCreatedAt($value)
 * @method static Builder<static>|Reservation whereDeletedAt($value)
 * @method static Builder<static>|Reservation whereDescuento($value)
 * @method static Builder<static>|Reservation whereDestinoDireccion($value)
 * @method static Builder<static>|Reservation whereDestinoLat($value)
 * @method static Builder<static>|Reservation whereDestinoLng($value)
 * @method static Builder<static>|Reservation whereDistanciaKm($value)
 * @method static Builder<static>|Reservation whereDuracionMinutos($value)
 * @method static Builder<static>|Reservation whereEstado($value)
 * @method static Builder<static>|Reservation whereFechaConfirmacion($value)
 * @method static Builder<static>|Reservation whereFechaFin($value)
 * @method static Builder<static>|Reservation whereFechaFinReal($value)
 * @method static Builder<static>|Reservation whereFechaInicio($value)
 * @method static Builder<static>|Reservation whereFechaInicioReal($value)
 * @method static Builder<static>|Reservation whereId($value)
 * @method static Builder<static>|Reservation whereMonto($value)
 * @method static Builder<static>|Reservation whereMontoFinal($value)
 * @method static Builder<static>|Reservation whereMotivoCancelacion($value)
 * @method static Builder<static>|Reservation whereNotasAdmin($value)
 * @method static Builder<static>|Reservation whereNotasCliente($value)
 * @method static Builder<static>|Reservation whereNotasConductor($value)
 * @method static Builder<static>|Reservation whereOrigenDireccion($value)
 * @method static Builder<static>|Reservation whereOrigenLat($value)
 * @method static Builder<static>|Reservation whereOrigenLng($value)
 * @method static Builder<static>|Reservation whereSedeId($value)
 * @method static Builder<static>|Reservation whereTipo($value)
 * @method static Builder<static>|Reservation whereUpdatedAt($value)
 * @method static Builder<static>|Reservation whereUserId($value)
 * @method static Builder<static>|Reservation whereVehiculoId($value)
 * @method static Builder<static>|Reservation whereWaypoints($value)
 * @method static Builder<static>|Reservation withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Reservation withoutTrashed()
 * @mixin \Eloquent
 */
class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo',
        'user_id',
        'vehiculo_id',
        'conductor_id',
        'sede_id',
        'tipo',
        'estado',
        'origen_direccion',
        'origen_lat',
        'origen_lng',
        'destino_direccion',
        'destino_lat',
        'destino_lng',
        'fecha_inicio',
        'fecha_fin',
        'fecha_confirmacion',
        'fecha_inicio_real',
        'fecha_fin_real',
        'monto',
        'descuento',
        'monto_final',
        'notas_cliente',
        'notas_conductor',
        'notas_admin',
        'waypoints',
        'distancia_km',
        'duracion_minutos',
        'calificacion_conductor',
        'comentario_conductor',
        'calificacion_cliente',
        'comentario_cliente',
        'motivo_cancelacion',
        'cancelado_por'
    ];

    protected $casts = [
        'tipo' => ReservationType::class,
        'estado' => ReservationStatus::class,
        'waypoints' => 'array',
        'origen_lat' => 'float',
        'origen_lng' => 'float',
        'destino_lat' => 'float',
        'destino_lng' => 'float',
        'monto' => 'decimal:2',
        'descuento' => 'decimal:2',
        'monto_final' => 'decimal:2',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'fecha_confirmacion' => 'datetime',
        'fecha_inicio_real' => 'datetime',
        'fecha_fin_real' => 'datetime',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehiculo_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'conductor_id');
    }

    public function driverProfile()
    {
        return $this->belongsTo(DriverProfile::class, 'conductor_id', 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'sede_id');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelado_por');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'reserva_id');
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class, 'reserva_id');
    }

    public function gpsTracks()
    {
        return $this->hasMany(GpsTrack::class, 'reserva_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'reserva_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('estado', ReservationStatus::Pendiente);
    }

    public function scopeConfirmadas(Builder $query): Builder
    {
        return $query->where('estado', ReservationStatus::Confirmada);
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('estado', ReservationStatus::Activa);
    }

    public function scopeCompletadas(Builder $query): Builder
    {
        return $query->where('estado', ReservationStatus::Completada);
    }

    public function scopeCanceladas(Builder $query): Builder
    {
        return $query->where('estado', ReservationStatus::Cancelada);
    }

    public function scopeReservas(Builder $query): Builder
    {
        return $query->where('tipo', ReservationType::Reserva);
    }

    public function scopeDomicilios(Builder $query): Builder
    {
        return $query->where('tipo', ReservationType::Domicilio);
    }

    public function scopeDelUsuario(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDelConductor(Builder $query, int $conductorId): Builder
    {
        return $query->where('conductor_id', $conductorId);
    }

    public function scopeEnRango(Builder $query, $desde, $hasta): Builder
    {
        return $query->whereBetween('fecha_inicio', [$desde, $hasta]);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Verificar si es una reserva de vehículo
     */
    public function isReserva(): bool
    {
        return $this->tipo === ReservationType::Reserva;
    }

    /**
     * Verificar si es un domicilio
     */
    public function isDomicilio(): bool
    {
        return $this->tipo === ReservationType::Domicilio;
    }

    /**
     * Verificar si está pendiente
     */
    public function isPendiente(): bool
    {
        return $this->estado === ReservationStatus::Pendiente;
    }

    /**
     * Verificar si está confirmada
     */
    public function isConfirmada(): bool
    {
        return $this->estado === ReservationStatus::Confirmada;
    }

    /**
     * Verificar si está activa
     */
    public function isActiva(): bool
    {
        return $this->estado === ReservationStatus::Activa;
    }

    /**
     * Verificar si está completada
     */
    public function isCompletada(): bool
    {
        return $this->estado === ReservationStatus::Completada;
    }

    /**
     * Verificar si está cancelada
     */
    public function isCancelada(): bool
    {
        return $this->estado === ReservationStatus::Cancelada;
    }

    /**
     * Puede ser cancelada
     */
    public function canBeCancelled(): bool
    {
        return $this->estado->canBeCancelled();
    }

    /**
     * Está en estado final
     */
    public function isFinal(): bool
    {
        return $this->estado->isFinal();
    }

    /**
     * Obtener pago aprobado
     */
    public function getApprovedPayment()
    {
        return $this->payments()->where('estado', 'aprobado')->first();
    }

    /**
     * Verificar si tiene pago aprobado
     */
    public function hasPaidPayment(): bool
    {
        return $this->payments()->where('estado', 'aprobado')->exists();
    }

    /**
     * Calcular duración real en minutos
     */
    public function getDuracionReal(): ?int
    {
        if ($this->fecha_inicio_real && $this->fecha_fin_real) {
            return $this->fecha_inicio_real->diffInMinutes($this->fecha_fin_real);
        }
        return null;
    }

    /**
     * Calcular duración estimada en minutos
     */
    public function getDuracionEstimada(): int
    {
        return $this->fecha_inicio->diffInMinutes($this->fecha_fin);
    }

    /**
     * Generar código único para la reserva
     */
    public static function generateCode(): string
    {
        $year = now()->year;
        $lastReservation = self::whereYear('created_at', $year)
            ->orderByDesc('id')
            ->first();
        
        $number = $lastReservation ? intval(substr($lastReservation->codigo, -4)) + 1 : 1;
        
        return sprintf('RES-%d-%04d', $year, $number);
    }

    /**
     * Boot method para generar código automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            if (empty($reservation->codigo)) {
                $reservation->codigo = self::generateCode();
            }
        });
    }
}