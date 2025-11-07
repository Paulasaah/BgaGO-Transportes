<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo_transaccion',
        'reserva_id',
        'user_id',
        'metodo_pago',
        'monto',
        'estado',
        'referencia_externa',
        'datos_transaccion',
        'motivo_rechazo',
        'fecha_aprobacion',
    ];

    protected $casts = [
        'monto' => 'float',
        'fecha_aprobacion' => 'datetime',
        'datos_transaccion' => 'array',
    ];

    // Relaciones
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reserva_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
