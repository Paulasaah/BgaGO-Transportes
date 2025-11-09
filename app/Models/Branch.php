<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'direccion',
        'ciudad',
        'telefono',
        'email',
        'latitud',
        'longitud',
        'radio',
        'color',
        'descripcion',
        'capacidad_vehiculos',
        'activa',
    ];

    protected $casts = [
        'latitud' => 'float',
        'longitud' => 'float',
        'radio' => 'integer',
        'activa' => 'boolean',
    ];

    // Relaciones
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'sede_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'sede_id');
    }
}
