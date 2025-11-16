<?php

namespace App\Models;

use App\Enums\MaintenanceType;
use App\Enums\MaintenanceStatus;
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
        'tipo' => MaintenanceType::class,
        'estado' => MaintenanceStatus::class,
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

    // Scopes
    public function scopeProgramados($query)
    {
        return $query->where('estado', MaintenanceStatus::Programado);
    }

    public function scopeEnProceso($query)
    {
        return $query->where('estado', MaintenanceStatus::EnProceso);
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', MaintenanceStatus::Completado);
    }

    public function scopeActivos($query)
    {
        return $query->whereIn('estado', [MaintenanceStatus::Programado, MaintenanceStatus::EnProceso]);
    }

    public function scopePreventivos($query)
    {
        return $query->where('tipo', MaintenanceType::Preventivo);
    }

    public function scopeCorrectivos($query)
    {
        return $query->where('tipo', MaintenanceType::Correctivo);
    }

    // Métodos helper
    public function isActive(): bool
    {
        return $this->estado->isActive();
    }

    public function isFinished(): bool
    {
        return $this->estado->isFinished();
    }

    public function canEdit(): bool
    {
        return $this->estado->canEdit();
    }

    public function canCancel(): bool
    {
        return $this->estado->canCancel();
    }

    /**
     * Marcar como completado
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'estado' => MaintenanceStatus::Completado,
            'fecha_realizada' => now(),
        ]);
    }

    /**
     * Cancelar mantenimiento
     */
    public function cancel(): void
    {
        $this->update([
            'estado' => MaintenanceStatus::Cancelado,
        ]);
    }

    /**
     * Iniciar mantenimiento
     */
    public function start(): void
    {
        $this->update([
            'estado' => MaintenanceStatus::EnProceso,
        ]);
    }
}
