<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
<<<<<<< HEAD
        'user_id',
        'vehicle_id',
        'driver_id',
        'service_type',
        'status',
        'pickup_address',
        'dropoff_address',
        'starts_at',
        'ends_at',
        'distance_km',
        'price_cents',
        'canceled_at',
        'cancellation_reason',
    ];

    // 🔗 Relaciones
=======
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
        'cancelado_por',
    ];

    protected $casts = [
        'origen_lat' => 'float',
        'origen_lng' => 'float',
        'destino_lat' => 'float',
        'destino_lng' => 'float',
        'waypoints' => 'array',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'fecha_confirmacion' => 'datetime',
        'fecha_inicio_real' => 'datetime',
        'fecha_fin_real' => 'datetime',
        'monto' => 'float',
        'descuento' => 'float',
        'monto_final' => 'float',
    ];

    // Relaciones
>>>>>>> sergio
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
<<<<<<< HEAD
        return $this->belongsTo(DriverProfile::class, 'driver_id');
=======
        return $this->belongsTo(User::class, 'conductor_id');
>>>>>>> sergio
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
}
