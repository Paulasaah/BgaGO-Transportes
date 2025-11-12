<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case Disponible = 'disponible';
    case Ocupado = 'ocupado';
    case Mantenimiento = 'mantenimiento';
    case Inactivo = 'inactivo';

    public function label(): string
    {
        return match($this) {
            self::Disponible => 'Disponible',
            self::Ocupado => 'En Uso',
            self::Mantenimiento => 'En Mantenimiento',
            self::Inactivo => 'Inactivo',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Disponible => 'check-circle',
            self::Ocupado => 'clock',
            self::Mantenimiento => 'wrench',
            self::Inactivo => 'x-circle',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Disponible => 'green',
            self::Ocupado => 'yellow',
            self::Mantenimiento => 'orange',
            self::Inactivo => 'gray',
        };
    }

    /**
     * Puede ser reservado
     */
    public function isAvailable(): bool
    {
        return $this === self::Disponible;
    }

    /**
     * Está en uso
     */
    public function isOccupied(): bool
    {
        return $this === self::Ocupado;
    }

    /**
     * Necesita atención
     */
    public function needsAttention(): bool
    {
        return in_array($this, [self::Mantenimiento, self::Inactivo]);
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label()
        ])->toArray();
    }
}