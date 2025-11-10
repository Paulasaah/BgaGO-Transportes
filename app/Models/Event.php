<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'tipo',
        'user_id',
        'vehiculo_id',
        'reserva_id',
        'titulo',
        'descripcion',
        'datos',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'datos' => 'array',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehiculo_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reserva_id');
    }
}
