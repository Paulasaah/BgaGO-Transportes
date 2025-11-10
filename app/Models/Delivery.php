<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reserva_id',
        'tipo',
        'descripcion',
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
        'lat_origen' => 'float',
        'lon_origen' => 'float',
        'lat_destino' => 'float',
        'lon_destino' => 'float',
        'costo' => 'float',
        'fecha_entrega_estimada' => 'datetime',
        'fecha_entrega_real' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reserva_id');
    }
}
