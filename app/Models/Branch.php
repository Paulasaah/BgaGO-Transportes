<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'branches';

    protected $fillable = [
        'nombre',
        'direccion',
        'ciudad',
        'telefono',
        'email',
        'latitud',
        'longitud',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'latitud' => 'float',
        'longitud' => 'float',
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
