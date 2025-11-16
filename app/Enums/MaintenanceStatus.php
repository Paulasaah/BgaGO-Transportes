<?php

namespace App\Enums;

enum MaintenanceStatus: string
{
    case Programado = 'programado';
    case EnProceso = 'en_proceso';
    case Completado = 'completado';
    case Cancelado = 'cancelado';

    /**
     * Obtener el label legible del estado
     */
    public function label(): string
    {
        return match($this) {
            self::Programado => 'Programado',
            self::EnProceso => 'En Proceso',
            self::Completado => 'Completado',
            self::Cancelado => 'Cancelado',
        };
    }

    /**
     * Obtener el icono del estado
     */
    public function icon(): string
    {
        return match($this) {
            self::Programado => 'calendar',
            self::EnProceso => 'cog',
            self::Completado => 'check-circle',
            self::Cancelado => 'x-circle',
        };
    }

    /**
     * Obtener el color del estado
     */
    public function color(): string
    {
        return match($this) {
            self::Programado => 'yellow',
            self::EnProceso => 'blue',
            self::Completado => 'green',
            self::Cancelado => 'red',
        };
    }

    /**
     * Verificar si el mantenimiento está activo
     */
    public function isActive(): bool
    {
        return in_array($this, [self::Programado, self::EnProceso]);
    }

    /**
     * Verificar si el mantenimiento está finalizado
     */
    public function isFinished(): bool
    {
        return in_array($this, [self::Completado, self::Cancelado]);
    }

    /**
     * Verificar si se puede editar
     */
    public function canEdit(): bool
    {
        return $this !== self::Completado;
    }

    /**
     * Verificar si se puede cancelar
     */
    public function canCancel(): bool
    {
        return in_array($this, [self::Programado, self::EnProceso]);
    }
}
