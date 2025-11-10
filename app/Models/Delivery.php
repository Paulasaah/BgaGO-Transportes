<?php

namespace App\Models;

use App\Enums\DeliveryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    public function getEstado(): string
    {
        return $this->reservation ? $this->reservation->estado->value : 'desconocido';
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
    public function getDistanciaKm(): float
    {
        return $this->reservation ? $this->reservation->distancia_km : 0;
    }

    /**
     * Calcular tiempo estimado de entrega (minutos)
     */
    public function calcularTiempoEstimado(): int
    {
        if (!$this->reservation) {
            return 0;
        }

        return $this->reservation->duracion_minutos;
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