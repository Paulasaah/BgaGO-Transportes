<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $tipo
 * @property int|null $user_id
 * @property int|null $vehiculo_id
 * @property int|null $reserva_id
 * @property string $titulo
 * @property string|null $descripcion
 * @property array<array-key, mixed>|null $datos
 * @property string|null $ip
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Reservation|null $reservation
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\Vehicle|null $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDatos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereReservaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereTitulo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereVehiculoId($value)
 * @mixin \Eloquent
 */
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
