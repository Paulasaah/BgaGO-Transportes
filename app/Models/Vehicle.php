<?php

namespace App\Models;

use App\Enums\VehicleStatus;
use App\Enums\VehicleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $placa
 * @property string $marca
 * @property string $modelo
 * @property int|null $year
 * @property VehicleType $tipo
 * @property string|null $color
 * @property int $sede_id
 * @property int|null $conductor_id
 * @property VehicleStatus $estado
 * @property bool $visible_catalogo
 * @property numeric $precio_hora
 * @property numeric $precio_dia
 * @property string|null $imagen_principal
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Reservation|null $activeReservation
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\GpsTrack|null $currentLocation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Delivery> $deliveries
 * @property-read int|null $deliveries_count
 * @property-read \App\Models\User|null $driver
 * @property-read \App\Models\DriverProfile|null $driverProfile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GpsTrack> $gpsTracks
 * @property-read int|null $gps_tracks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VehicleMaintenance> $maintenances
 * @property-read int|null $maintenances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * @property-read int|null $reservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Telemetria> $telemetrias
 * @property-read int|null $telemetrias_count
 * @method static Builder<static>|Vehicle conConductor()
 * @method static Builder<static>|Vehicle disponibles()
 * @method static Builder<static>|Vehicle enMantenimiento()
 * @method static Builder<static>|Vehicle inactivos()
 * @method static Builder<static>|Vehicle newModelQuery()
 * @method static Builder<static>|Vehicle newQuery()
 * @method static Builder<static>|Vehicle ocupados()
 * @method static Builder<static>|Vehicle onlyTrashed()
 * @method static Builder<static>|Vehicle porSede(int $sedeId)
 * @method static Builder<static>|Vehicle porTipo(\App\Enums\VehicleType|string $tipo)
 * @method static Builder<static>|Vehicle query()
 * @method static Builder<static>|Vehicle sinConductor()
 * @method static Builder<static>|Vehicle visiblesEnCatalogo()
 * @method static Builder<static>|Vehicle whereColor($value)
 * @method static Builder<static>|Vehicle whereConductorId($value)
 * @method static Builder<static>|Vehicle whereCreatedAt($value)
 * @method static Builder<static>|Vehicle whereDeletedAt($value)
 * @method static Builder<static>|Vehicle whereDescripcion($value)
 * @method static Builder<static>|Vehicle whereEstado($value)
 * @method static Builder<static>|Vehicle whereId($value)
 * @method static Builder<static>|Vehicle whereImagenPrincipal($value)
 * @method static Builder<static>|Vehicle whereMarca($value)
 * @method static Builder<static>|Vehicle whereModelo($value)
 * @method static Builder<static>|Vehicle wherePlaca($value)
 * @method static Builder<static>|Vehicle wherePrecioDia($value)
 * @method static Builder<static>|Vehicle wherePrecioHora($value)
 * @method static Builder<static>|Vehicle whereSedeId($value)
 * @method static Builder<static>|Vehicle whereTipo($value)
 * @method static Builder<static>|Vehicle whereUpdatedAt($value)
 * @method static Builder<static>|Vehicle whereVisibleCatalogo($value)
 * @method static Builder<static>|Vehicle whereYear($value)
 * @method static Builder<static>|Vehicle withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Vehicle withoutTrashed()
 * @mixin \Eloquent
 */
class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'placa',
        'marca',
        'modelo',
        'year',
        'tipo',
        'color',
        'sede_id',
        'conductor_id',
        'estado',
        'visible_catalogo',
        'precio_hora',
        'precio_dia',
        'imagen_principal',
        'descripcion',
    ];

    protected $casts = [
        'tipo' => VehicleType::class,
        'estado' => VehicleStatus::class,
        'visible_catalogo' => 'boolean',
        'precio_hora' => 'decimal:2',
        'precio_dia' => 'decimal:2',
        'year' => 'integer',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'sede_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'conductor_id');
    }

    public function driverProfile()
    {
        return $this->belongsTo(DriverProfile::class, 'conductor_id', 'user_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'vehiculo_id');
    }

    public function maintenances()
    {
        return $this->hasMany(VehicleMaintenance::class, 'vehiculo_id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'vehiculo_id');
    }

    public function gpsTracks()
    {
        return $this->hasMany(GpsTrack::class, 'vehiculo_id');
    }

    public function telemetrias()
    {
        return $this->hasMany(Telemetria::class, 'device_id', 'placa');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeDisponibles(Builder $query): Builder
    {
        return $query->where('estado', VehicleStatus::Disponible)
                    ->where('visible_catalogo', true);
    }

    public function scopeOcupados(Builder $query): Builder
    {
        return $query->where('estado', VehicleStatus::Ocupado);
    }

    public function scopeEnMantenimiento(Builder $query): Builder
    {
        return $query->where('estado', VehicleStatus::Mantenimiento);
    }

    public function scopeInactivos(Builder $query): Builder
    {
        return $query->where('estado', VehicleStatus::Inactivo);
    }

    public function scopePorTipo(Builder $query, VehicleType|string $tipo): Builder
    {
        return $query->where('tipo', $tipo instanceof VehicleType ? $tipo : VehicleType::from($tipo));
    }

    public function scopePorSede(Builder $query, int $sedeId): Builder
    {
        return $query->where('sede_id', $sedeId);
    }

    public function scopeVisiblesEnCatalogo(Builder $query): Builder
    {
        return $query->where('visible_catalogo', true);
    }

    public function scopeConConductor(Builder $query): Builder
    {
        return $query->whereNotNull('conductor_id');
    }

    public function scopeSinConductor(Builder $query): Builder
    {
        return $query->whereNull('conductor_id');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Verificar si está disponible
     */
    public function isDisponible(): bool
    {
        return $this->estado === VehicleStatus::Disponible;
    }

    /**
     * Verificar si está ocupado
     */
    public function isOcupado(): bool
    {
        return $this->estado === VehicleStatus::Ocupado;
    }

    /**
     * Verificar si necesita mantenimiento
     */
    public function needsMaintenance(): bool
    {
        return $this->estado === VehicleStatus::Mantenimiento;
    }

    /**
     * Verificar si tiene conductor asignado
     */
    public function hasDriver(): bool
    {
        return !is_null($this->conductor_id);
    }

    /**
     * Obtener reserva activa
     */
    public function getActiveReservation()
    {
        return $this->reservations()
            ->whereIn('estado', ['confirmada', 'activa'])
            ->orderByDesc('fecha_inicio')
            ->first();
    }

    /**
     * Verificar disponibilidad en rango de fechas
     */
    public function isAvailableInRange($fechaInicio, $fechaFin): bool
    {
        if (!$this->isDisponible()) {
            return false;
        }

        return !$this->reservations()
            ->whereIn('estado', ['pendiente', 'confirmada', 'activa'])
            ->where(function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                    ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                    ->orWhere(function ($q) use ($fechaInicio, $fechaFin) {
                        $q->where('fecha_inicio', '<=', $fechaInicio)
                          ->where('fecha_fin', '>=', $fechaFin);
                    });
            })
            ->exists();
    }

    /**
     * Obtener última telemetría
     */
    public function getLatestTelemetry()
    {
        return $this->telemetrias()->latest()->first();
    }

    /**
     * Obtener ubicación actual
     */
    public function getCurrentLocation(): ?array
    {
        $telemetry = $this->getLatestTelemetry();

        if (!$telemetry) {
            return null;
        }

        return [
            'lat' => $telemetry->lat,
            'lon' => $telemetry->lon,
            'battery' => $telemetry->battery,
            'status' => $telemetry->status,
        ];
    }

    /**
     * Calcular total de ingresos generados
     */
    public function getTotalIngresos(): float
    {
        return $this->reservations()
            ->where('estado', 'completada')
            ->sum('monto_final');
    }

    /**
     * Calcular promedio de calificación
     */
    public function getAverageRating(): ?float
    {
        $avg = $this->reservations()
            ->where('estado', 'completada')
            ->whereNotNull('calificacion_cliente')
            ->avg('calificacion_cliente');

        return $avg ? round($avg, 1) : null;
    }

    /**
     * Obtener nombre completo del vehículo
     */
    public function getFullName(): string
    {
        return "{$this->tipo->label()} {$this->marca} {$this->modelo}";
    }

    // Agregar estos métodos al final de Vehicle.php, antes del último }

    /**
     * Obtener ubicación GPS actual (para DataService)
     */
    public function currentLocation()
    {
        return $this->hasOne(GpsTrack::class, 'vehiculo_id')
            ->latest('fecha_registro');
    }

    /**
     * Obtener reserva activa (para DataService)
     */
    public function activeReservation()
    {
        return $this->hasOne(Reservation::class, 'vehiculo_id')
            ->where('estado', \App\Enums\ReservationStatus::Activa);
    }

    /**
     * Cambiar estado del vehículo
     */
    public function changeStatus(VehicleStatus $newStatus, ?string $reason = null): void
    {
        $oldStatus = $this->estado;
        $this->estado = $newStatus;
        $this->save();

        // Registrar evento
        Event::create([
            'tipo' => 'vehiculo_cambio_estado',
            'vehiculo_id' => $this->id,
            'titulo' => "Estado cambiado: {$oldStatus->label()} → {$newStatus->label()}",
            'descripcion' => $reason,
            'datos' => [
                'estado_anterior' => $oldStatus->value,
                'estado_nuevo' => $newStatus->value,
            ],
        ]);
    }

}
