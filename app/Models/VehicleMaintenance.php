<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleMaintenance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vehicle_maintenances';

    protected $fillable = [
        'vehiculo_id',
        'realizado_por',
        'tipo',
        'estado',
        'kilometraje_actual',
        'kilometraje_proximo',
        'kilometraje_intervalo',
        'fecha_programada',
        'fecha_realizada',
        'costo',
        'descripcion',
        'repuestos_usados',
        'taller',
        'mecanico',
        'observaciones',
        'archivos',
    ];

    protected $casts = [
        'costo' => 'float',
        'fecha_programada' => 'date',
        'fecha_realizada' => 'date',
        'archivos' => 'array',
    ];

    // Relaciones
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehiculo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'realizado_por');
    }
}
