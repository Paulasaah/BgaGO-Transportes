<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $license_number
 * @property float $rating
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * @property-read int|null $reservations_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Vehicle> $vehicles
 * @property-read int|null $vehicles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverProfile whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverProfile whereLicenseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverProfile whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverProfile whereUserId($value)
 * @mixin \Eloquent
 */
class DriverProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
        'license_expiry',
        'rating',
        'is_active',
    ];

    protected $casts = [
        'rating' => 'float',
        'is_active' => 'boolean',
        'license_expiry' => 'date',
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
