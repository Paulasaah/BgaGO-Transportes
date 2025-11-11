<?php

namespace App\Enums;

enum ReservationType: string
{
    case Reserva = 'reserva';
    case Domicilio = 'domicilio';

    /**
     * Obtener label legible
     */
    public function label(): string
    {
        return match($this) {
            self::Reserva => 'Reserva de Vehículo',
            self::Domicilio => 'Servicio de Domicilio',
        };
    }

    /**
     * Obtener icono Lucide
     */
    public function icon(): string
    {
        return match($this) {
            self::Reserva => 'calendar-check',
            self::Domicilio => 'package',
        };
    }

    /**
     * Obtener color Flux/Tailwind
     */
    public function color(): string
    {
        return match($this) {
            self::Reserva => 'blue',
            self::Domicilio => 'orange',
        };
    }

    /**
     * Obtener todos los valores
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Obtener opciones para select
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label()
        ])->toArray();
    }

    public function description(): string
    {
        return match($this) {
            self::Domicilio => 'Domicilio',
            self::ReservaVehiculo => 'Reserva de vehículo',
            default => 'Otro tipo',
        };
    }

}