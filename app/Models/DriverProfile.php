<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'documento',
        'telefono',
        'email',
        'licencia',
        'experiencia',
        'estado',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
