<?php

namespace App\Enums;

enum DeliveryType: string
{
    case Paquete = 'paquete';
    case Vehiculo = 'vehiculo';

    public function label(): string
    {
        return match($this) {
            self::Paquete => 'Envío de Paquete',
            self::Vehiculo => 'Transporte de Vehículo',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::Paquete => 'Envío de objetos, documentos y paquetes',
            self::Vehiculo => 'Te llevamos el medio de transporte hasta tu ubicación',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Paquete => 'box',
            self::Vehiculo => 'truck',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Paquete => 'blue',
            self::Vehiculo => 'green',
        };
    }

    /**
     * Requiere vehículo asociado
     */
    public function requiresVehicle(): bool
    {
        return $this === self::Vehiculo;
    }

    /**
     * Permite descripción de contenido
     */
    public function allowsDescription(): bool
    {
        return $this === self::Paquete;
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