<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'visible_catalogo' => 'boolean',
        'precio_hora' => 'float',
        'precio_dia' => 'float',
    ];

    // Relaciones
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'sede_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'conductor_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'vehiculo_id');
    }
}
