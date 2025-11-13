<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class DriverProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
        'rating',
        'is_active',
    ];

    protected $casts = [
        'rating' => 'float',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'conductor_id');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'conductor_id');
    }
}
