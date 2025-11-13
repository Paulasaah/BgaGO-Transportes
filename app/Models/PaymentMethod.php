<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $nombre
 * @property string $tipo
 * @property bool $activo
 * @property string|null $descripcion
 * @property array<array-key, mixed>|null $config Configuración adicional, ej: claves API
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo',
        'activo',
        'descripcion',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
        'activo' => 'boolean',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'metodo_pago', 'tipo');
    }
}
