<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int|null $payment_id
 * @property int|null $user_id
 * @property string $accion
 * @property string|null $descripcion
 * @property array<array-key, mixed>|null $datos
 * @property string|null $ip
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereAccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereDatos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereUserId($value)
 * @mixin \Eloquent
 */
class TransactionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'user_id',
        'accion',
        'descripcion',
        'datos',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'datos' => 'array',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
