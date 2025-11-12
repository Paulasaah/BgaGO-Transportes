<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

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