<?php

namespace App\Enums;

enum VehicleType: string
{
    case Moto = 'moto';
    case Bicicleta = 'bicicleta';
    case Scooter = 'scooter';
    case Patineta = 'patineta';

    public function label(): string
    {
        return match($this) {
            self::Moto => 'Moto Eléctrica',
            self::Bicicleta => 'Bicicleta Eléctrica',
            self::Scooter => 'Scooter Eléctrico',
            self::Patineta => 'Patineta Eléctrica',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Moto => 'bike',
            self::Bicicleta => 'bicycle',
            self::Scooter => 'bike',
            self::Patineta => 'move',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Moto => 'red',
            self::Bicicleta => 'green',
            self::Scooter => 'blue',
            self::Patineta => 'purple',
        };
    }

    /**
     * Precio base por hora (en COP)
     */
    public function baseHourlyRate(): int
    {
        return match($this) {
            self::Moto => 8000,
            self::Bicicleta => 5000,
            self::Scooter => 4000,
            self::Patineta => 4000,
        };
    }

    /**
     * Precio base por día (en COP)
     */
    public function baseDailyRate(): int
    {
        return match($this) {
            self::Moto => 50000,
            self::Bicicleta => 30000,
            self::Scooter => 25000,
            self::Patineta => 25000,
        };
    }

    /**
     * Capacidad de carga (kg)
     */
    public function loadCapacity(): int
    {
        return match($this) {
            self::Moto => 100,
            self::Bicicleta => 20,
            self::Scooter => 15,
            self::Patineta => 10,
        };
    }

    /**
     * Velocidad máxima (km/h)
     */
    public function maxSpeed(): int
    {
        return match($this) {
            self::Moto => 60,
            self::Bicicleta => 25,
            self::Scooter => 25,
            self::Patineta => 20,
        };
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