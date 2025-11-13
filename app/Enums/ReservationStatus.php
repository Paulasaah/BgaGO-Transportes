<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case Pendiente = 'pendiente';
    case Confirmada = 'confirmada';
    case Activa = 'activa';
    case Completada = 'completada';
    case Cancelada = 'cancelada';

    public function label(): string
    {
        return match($this) {
            self::Pendiente => 'Pendiente',
            self::Confirmada => 'Confirmada',
            self::Activa => 'En Curso',
            self::Completada => 'Completada',
            self::Cancelada => 'Cancelada',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Pendiente => 'clock',
            self::Confirmada => 'check-circle',
            self::Activa => 'play-circle',
            self::Completada => 'check-check',
            self::Cancelada => 'x-circle',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pendiente => 'yellow',
            self::Confirmada => 'blue',
            self::Activa => 'green',
            self::Completada => 'gray',
            self::Cancelada => 'red',
        };
    }

    /**
     * Estados que permiten cancelación
     */
    public function canBeCancelled(): bool
    {
        return in_array($this, [self::Pendiente, self::Confirmada]);
    }

    /**
     * Estados que indican servicio activo
     */
    public function isActive(): bool
    {
        return $this === self::Activa;
    }

    /**
     * Estados finales (no se pueden modificar)
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::Completada, self::Cancelada]);
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