<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $clave
 * @property string $valor
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereClave($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereValor($value)
 * @mixin \Eloquent
 */
class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuraciones'; // 👈 nombre explícito de la tabla

    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
    ];
}
