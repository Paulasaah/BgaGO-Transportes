<?php

namespace App\Models;

use App\Enums\DeliveryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $vehiculo_id
 * @property int|null $reserva_id
 * @property int|null $conductor_id
 * @property DeliveryType $tipo
 * @property string|null $descripcion
 * @property string|null $nombre_remitente
 * @property string|null $telefono_remitente
 * @property string|null $nombre_destinatario
 * @property string|null $telefono_destinatario
 * @property string|null $descripcion_contenido
 * @property numeric|null $peso_estimado
 * @property bool $es_fragil
 * @property string|null $instrucciones_especiales
 * @property string $direccion_origen
 * @property string|null $lat_origen
 * @property string|null $lon_origen
 * @property string $direccion_destino
 * @property string|null $lat_destino
 * @property string|null $lon_destino
 * @property numeric $costo
 * @property string $estado
 * @property \Illuminate\Support\Carbon|null $fecha_entrega_estimada
 * @property \Illuminate\Support\Carbon|null $fecha_entrega_real
 * @property \Illuminate\Support\Carbon|null $fecha_recogida
 * @property \Illuminate\Support\Carbon|null $fecha_entrega
 * @property string|null $notas_entrega
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Reservation|null $reservation
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Vehicle|null $vehicle
 * @method static Builder<static>|Delivery activos()
 * @method static Builder<static>|Delivery completados()
 * @method static Builder<static>|Delivery delUsuario(int $userId)
 * @method static Builder<static>|Delivery newModelQuery()
 * @method static Builder<static>|Delivery newQuery()
 * @method static Builder<static>|Delivery onlyTrashed()
 * @method static Builder<static>|Delivery paquetes()
 * @method static Builder<static>|Delivery pendientes()
 * @method static Builder<static>|Delivery query()
 * @method static Builder<static>|Delivery vehiculos()
 * @method static Builder<static>|Delivery whereConductorId($value)
 * @method static Builder<static>|Delivery whereCosto($value)
 * @method static Builder<static>|Delivery whereCreatedAt($value)
 * @method static Builder<static>|Delivery whereDeletedAt($value)
 * @method static Builder<static>|Delivery whereDescripcion($value)
 * @method static Builder<static>|Delivery whereDescripcionContenido($value)
 * @method static Builder<static>|Delivery whereDireccionDestino($value)
 * @method static Builder<static>|Delivery whereDireccionOrigen($value)
 * @method static Builder<static>|Delivery whereEsFragil($value)
 * @method static Builder<static>|Delivery whereEstado($value)
 * @method static Builder<static>|Delivery whereFechaEntrega($value)
 * @method static Builder<static>|Delivery whereFechaEntregaEstimada($value)
 * @method static Builder<static>|Delivery whereFechaEntregaReal($value)
 * @method static Builder<static>|Delivery whereFechaRecogida($value)
 * @method static Builder<static>|Delivery whereId($value)
 * @method static Builder<static>|Delivery whereInstruccionesEspeciales($value)
 * @method static Builder<static>|Delivery whereLatDestino($value)
 * @method static Builder<static>|Delivery whereLatOrigen($value)
 * @method static Builder<static>|Delivery whereLonDestino($value)
 * @method static Builder<static>|Delivery whereLonOrigen($value)
 * @method static Builder<static>|Delivery whereNombreDestinatario($value)
 * @method static Builder<static>|Delivery whereNombreRemitente($value)
 * @method static Builder<static>|Delivery whereNotasEntrega($value)
 * @method static Builder<static>|Delivery wherePesoEstimado($value)
 * @method static Builder<static>|Delivery whereReservaId($value)
 * @method static Builder<static>|Delivery whereTelefonoDestinatario($value)
 * @method static Builder<static>|Delivery whereTelefonoRemitente($value)
 * @method static Builder<static>|Delivery whereTipo($value)
 * @method static Builder<static>|Delivery whereUpdatedAt($value)
 * @method static Builder<static>|Delivery whereUserId($value)
 * @method static Builder<static>|Delivery whereVehiculoId($value)
 * @method static Builder<static>|Delivery withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Delivery withoutTrashed()
 * @mixin \Eloquent
 */
class Delivery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'reserva_id',
        'tipo',
        'descripcion',
        'vehiculo_id',
        'nombre_remitente',
        'telefono_remitente',
        'nombre_destinatario',
        'telefono_destinatario',
        'descripcion_contenido',
        'peso_estimado',
        'es_fragil',
        'instrucciones_especiales',
        'fecha_recogida',
        'fecha_entrega',
        'notas_entrega',
        'direccion_origen',
        'lat_origen',
        'lon_origen',
        'direccion_destino',
        'lat_destino',
        'lon_destino',
        'costo',
        'estado',
        'fecha_entrega_estimada',
        'fecha_entrega_real',
    ];

    protected $casts = [
        'tipo' => DeliveryType::class,
        'peso_estimado' => 'decimal:2',
        'requiere_firma' => 'boolean',
        'es_fragil' => 'boolean',
        'fecha_recogida' => 'datetime',
        'fecha_entrega' => 'datetime',
        'fecha_entrega_estimada' => 'datetime',
        'fecha_entrega_real' => 'datetime',
        'costo' => 'decimal:2',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reserva_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehiculo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopePaquetes(Builder $query): Builder
    {
        return $query->where('tipo', DeliveryType::Paquete);
    }

    public function scopeVehiculos(Builder $query): Builder
    {
        return $query->where('tipo', DeliveryType::Vehiculo);
    }

    public function scopeDelUsuario(Builder $query, int $userId): Builder
    {
        return $query->whereHas('reservation', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->whereHas('reservation', function ($q) {
            $q->where('estado', 'pendiente');
        });
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->whereHas('reservation', function ($q) {
            $q->whereIn('estado', ['confirmada', 'activa']);
        });
    }

    public function scopeCompletados(Builder $query): Builder
    {
        return $query->whereHas('reservation', function ($q) {
            $q->where('estado', 'completada');
        });
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Verificar si es envío de paquete
     */
    public function isPaquete(): bool
    {
        return $this->tipo === DeliveryType::Paquete;
    }

    /**
     * Verificar si es transporte de vehículo
     */
    public function isVehiculo(): bool
    {
        return $this->tipo === DeliveryType::Vehiculo;
    }

    /**
     * Obtener estado del delivery (desde reservation)
     */
    public function getEstado(): array
    {
        return [
            'value' => $this->estado,
            'label' => $this->getEstadoLabel(),
            'icon' => $this->getEstadoIcon(),
            'color' => $this->getEstadoColor(),
        ];
    }

    /**
     * Obtener label del estado
     */
    private function getEstadoLabel(): string
    {
        return match($this->estado) {
            'pendiente' => 'Pendiente',
            'asignado' => 'Asignado',
            'confirmado' => 'Confirmado',
            'en_camino' => 'En Camino',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado',
            default => ucfirst($this->estado ?? 'Desconocido'),
        };
    }

    /**
     * Obtener icono del estado
     */
    private function getEstadoIcon(): string
    {
        return match($this->estado) {
            'pendiente' => 'clock',
            'asignado' => 'user-check',
            'confirmado' => 'check-circle',
            'en_camino' => 'truck',
            'entregado' => 'package-check',
            'cancelado' => 'x-circle',
            default => 'help-circle',
        };
    }

    /**
     * Obtener color del estado
     */
    private function getEstadoColor(): string
    {
        return match($this->estado) {
            'pendiente' => 'gray',
            'asignado' => 'blue',
            'confirmado' => 'cyan',
            'en_camino' => 'yellow',
            'entregado' => 'green',
            'cancelado' => 'red',
            default => 'gray',
        };
    }

    /**
     * Calcular tiempo estimado en minutos
     */
    public function calcularTiempoEstimado(): int
    {
        // Si no hay reserva, retornar tiempo por defecto
        if (!$this->reservation) {
            return 30;
        }

        // Si hay duración en la reserva, usarla
        if ($this->reservation->duracion_minutos) {
            return (int) $this->reservation->duracion_minutos;
        }

        // Si hay distancia, calcular tiempo (aproximadamente 30 km/h en ciudad)
        if ($this->reservation->distancia_km) {
            return (int) ceil($this->reservation->distancia_km * 2);
        }

        // Tiempo por defecto: 30 minutos
        return 30;
    }

    /**
     * Verificar si fue entregado
     */
    public function isEntregado(): bool
    {
        return $this->reservation && $this->reservation->isCompletada();
    }

    /**
     * Calcular distancia desde reservation
     */
    public function getDistanciaKm(): ?float
    {
        if (!$this->reservation) {
            return 0.0;
        }

        return (float) ($this->reservation->distancia_km ?? 0.0);
    }

    /**
     * Obtener tiempo transcurrido desde inicio
     */
    public function getTiempoTranscurrido(): ?int
    {
        if (!$this->reservation || !$this->reservation->fecha_inicio_real) {
            return null;
        }

        return now()->diffInMinutes($this->reservation->fecha_inicio_real);
    }

    /**
     * Verificar si está retrasado
     */
    public function isRetrasado(): bool
    {
        if (!$this->reservation || !$this->reservation->fecha_fin) {
            return false;
        }

        return now()->isAfter($this->reservation->fecha_fin) && !$this->isEntregado();
    }

    /**
     * Obtener direcciones desde reservation
     */
    public function getOrigen(): array
    {
        if (!$this->reservation) {
            return [];
        }

        return [
            'direccion' => $this->reservation->origen_direccion,
            'lat' => $this->reservation->origen_lat,
            'lng' => $this->reservation->origen_lng,
        ];
    }

    public function getDestino(): array
    {
        if (!$this->reservation) {
            return [];
        }

        return [
            'direccion' => $this->reservation->destino_direccion,
            'lat' => $this->reservation->destino_lat,
            'lng' => $this->reservation->destino_lng,
        ];
    }

    /**
     * Verificar si puede iniciar
     */
    public function canStart(): bool
    {
        return $this->reservation &&
               $this->reservation->isConfirmada() &&
               !$this->fecha_recogida;
    }

    /**
     * Verificar si puede completar
     */
    public function canComplete(): bool
    {
        return $this->reservation &&
               $this->reservation->isActiva() &&
               $this->fecha_recogida &&
               !$this->fecha_entrega;
    }

    /**
     * Obtener conductor asignado
     */
    public function getDriver()
    {
        return $this->reservation ? $this->reservation->driver : null;
    }
}
