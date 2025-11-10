<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
