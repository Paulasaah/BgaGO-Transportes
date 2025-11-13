<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
