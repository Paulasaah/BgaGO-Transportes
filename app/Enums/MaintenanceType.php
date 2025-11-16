<?php

namespace App\Enums;

enum MaintenanceType: string
{
    case Preventivo = 'preventivo';
    case Correctivo = 'correctivo';
    case RevisionGeneral = 'revision_general';
    case CambioAceite = 'cambio_aceite';
    case Llantas = 'llantas';
    case Frenos = 'frenos';
    case Bateria = 'bateria';
    case Cadena = 'cadena';
    case AjusteGeneral = 'ajuste_general';
    case Otro = 'otro';

    /**
     * Obtener el label legible del tipo de mantenimiento
     */
    public function label(): string
    {
        return match($this) {
            self::Preventivo => 'Mantenimiento Preventivo',
            self::Correctivo => 'Mantenimiento Correctivo',
            self::RevisionGeneral => 'Revisión General',
            self::CambioAceite => 'Cambio de Aceite',
            self::Llantas => 'Llantas',
            self::Frenos => 'Frenos',
            self::Bateria => 'Batería',
            self::Cadena => 'Cadena',
            self::AjusteGeneral => 'Ajuste General',
            self::Otro => 'Otro',
        };
    }

    /**
     * Obtener el icono del tipo de mantenimiento
     */
    public function icon(): string
    {
        return match($this) {
            self::Preventivo => 'calendar',
            self::Correctivo => 'wrench-screwdriver',
            self::RevisionGeneral => 'clipboard-document-check',
            self::CambioAceite => 'beaker',
            self::Llantas => 'circle-stack',
            self::Frenos => 'stop',
            self::Bateria => 'bolt',
            self::Cadena => 'link',
            self::AjusteGeneral => 'cog',
            self::Otro => 'ellipsis-horizontal',
        };
    }

    /**
     * Obtener el color del tipo de mantenimiento
     */
    public function color(): string
    {
        return match($this) {
            self::Preventivo => 'blue',
            self::Correctivo => 'red',
            self::RevisionGeneral => 'purple',
            self::CambioAceite => 'yellow',
            self::Llantas => 'gray',
            self::Frenos => 'orange',
            self::Bateria => 'green',
            self::Cadena => 'indigo',
            self::AjusteGeneral => 'cyan',
            self::Otro => 'zinc',
        };
    }

    /**
     * Obtener descripción del tipo
     */
    public function description(): string
    {
        return match($this) {
            self::Preventivo => 'Mantenimiento programado para prevenir fallas',
            self::Correctivo => 'Reparación de fallas o daños existentes',
            self::RevisionGeneral => 'Inspección completa del vehículo',
            self::CambioAceite => 'Cambio de aceite y filtros',
            self::Llantas => 'Cambio o reparación de llantas',
            self::Frenos => 'Revisión y reparación del sistema de frenos',
            self::Bateria => 'Cambio o mantenimiento de batería',
            self::Cadena => 'Ajuste o cambio de cadena de transmisión',
            self::AjusteGeneral => 'Ajustes mecánicos generales',
            self::Otro => 'Otro tipo de mantenimiento',
        };
    }

    /**
     * Obtener intervalo recomendado en kilómetros
     */
    public function recommendedInterval(): ?int
    {
        return match($this) {
            self::Preventivo => 5000,
            self::CambioAceite => 3000,
            self::RevisionGeneral => 10000,
            self::Llantas => 15000,
            self::Frenos => 8000,
            self::Bateria => 20000,
            self::Cadena => 5000,
            self::AjusteGeneral => 7000,
            default => null,
        };
    }
}
