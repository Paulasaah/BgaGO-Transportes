<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'driver_profile_id',
        'fecha_reserva',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'monto_total',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(DriverProfile::class, 'driver_profile_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
