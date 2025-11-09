<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
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
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(DriverProfile::class, 'driver_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
