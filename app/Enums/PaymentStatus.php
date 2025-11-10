<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pendiente = 'pendiente';
    case Aprobado = 'aprobado';
    case Rechazado = 'rechazado';
    case Reembolsado = 'reembolsado';

    public function label(): string
    {
        return match($this) {
            self::Pendiente => 'Pendiente',
            self::Aprobado => 'Aprobado',
            self::Rechazado => 'Rechazado',
            self::Reembolsado => 'Reembolsado',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Pendiente => 'clock',
            self::Aprobado => 'check-circle',
            self::Rechazado => 'x-circle',
            self::Reembolsado => 'rotate-ccw',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pendiente => 'yellow',
            self::Aprobado => 'green',
            self::Rechazado => 'red',
            self::Reembolsado => 'blue',
        };
    }

    /**
     * Pago exitoso
     */
    public function isSuccessful(): bool
    {
        return $this === self::Aprobado;
    }

    /**
     * Pago fallido
     */
    public function isFailed(): bool
    {
        return $this === self::Rechazado;
    }

    /**
     * Estado final (no se puede modificar)
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::Aprobado, self::Rechazado, self::Reembolsado]);
    }

    /**
     * Puede ser reembolsado
     */
    public function canBeRefunded(): bool
    {
        return $this === self::Aprobado;
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