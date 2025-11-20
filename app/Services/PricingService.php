<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Enums\VehicleType;
use App\Enums\DeliveryType;
use Carbon\Carbon;

class PricingService extends BaseService
{
    // Tarifas base por tipo de vehículo (COP)
    protected const BASE_RATES = [
        'moto' => [
            'hora' => 8000,
            'dia' => 50000,
            'semana' => 300000,
        ],
        'bicicleta' => [
            'hora' => 5000,
            'dia' => 30000,
            'semana' => 180000,
        ],
        'scooter' => [
            'hora' => 4000,
            'dia' => 25000,
            'semana' => 150000,
        ],
        'patineta' => [
            'hora' => 4000,
            'dia' => 25000,
            'semana' => 150000,
        ],
    ];

    // Tarifas de domicilios
    protected const DELIVERY_RATES = [
        'tarifa_base' => 2000,
        'por_km_paquete' => 1500,
        'por_km_vehiculo' => 1500,
        'tarifa_minima' => 2000,
    ];

    /**
     * Calcular precio de reserva de vehículo
     */
    public function calculateReservationPrice(
        Vehicle $vehicle,
        Carbon $fechaInicio,
        Carbon $fechaFin
    ): array {
        return $this->execute(function () use ($vehicle, $fechaInicio, $fechaFin) {
            $duracionMinutos = $fechaInicio->diffInMinutes($fechaFin);
            $duracionHoras = ceil($duracionMinutos / 60);
            $duracionDias = ceil($duracionMinutos / (60 * 24));

            $tipoVehiculo = $vehicle->tipo->value;
            $rates = self::BASE_RATES[$tipoVehiculo];

            // Determinar tarifa según duración
            if ($duracionHoras <= 24) {
                // Tarifa por hora
                $subtotal = $rates['hora'] * $duracionHoras;
                $tipoPrecio = 'hora';
            } elseif ($duracionDias <= 7) {
                // Tarifa por día
                $subtotal = $rates['dia'] * $duracionDias;
                $tipoPrecio = 'dia';
            } else {
                // Tarifa por semana + días extra
                $semanas = floor($duracionDias / 7);
                $diasExtra = $duracionDias % 7;
                $subtotal = ($rates['semana'] * $semanas) + ($rates['dia'] * $diasExtra);
                $tipoPrecio = 'semana';
            }

            // Aplicar descuentos
            $descuento = $this->calculateDiscount($subtotal, $duracionDias);
            $total = $subtotal - $descuento;

            return [
                'subtotal' => round($subtotal, 2),
                'descuento' => round($descuento, 2),
                'total' => round($total, 2),
                'detalles' => [
                    'tipo_vehiculo' => $vehicle->tipo->label(),
                    'duracion_minutos' => $duracionMinutos,
                    'duracion_horas' => $duracionHoras,
                    'duracion_dias' => $duracionDias,
                    'tipo_precio' => $tipoPrecio,
                    'tarifa_base' => $rates[$tipoPrecio === 'semana' ? 'dia' : $tipoPrecio],
                ]
            ];
        }, 'calcular_precio_reserva');
    }

    /**
     * Calcular precio de domicilio
     */
    public function calculateDeliveryPrice(
        DeliveryType $tipo,
        float $distanciaKm,
        ?Vehicle $vehicle = null,
        array $options = []
    ): array {
        return $this->execute(function () use ($tipo, $distanciaKm, $vehicle, $options) {
            $tarifaBase = self::DELIVERY_RATES['tarifa_base'];

            // Calcular costo por distancia
            if ($tipo === DeliveryType::Paquete) {
                $costoPorKm = self::DELIVERY_RATES['por_km_paquete'];
            } else {
                $costoPorKm = self::DELIVERY_RATES['por_km_vehiculo'];
            }

            $costoDistancia = $distanciaKm * $costoPorKm;
            $subtotal = $tarifaBase + $costoDistancia;

            // Recargo por tamaño del paquete (si aplica)
            if ($tipo === DeliveryType::Paquete) {
                $tamano = $options['tamano'] ?? null; // pequeno | mediano | grande
                $recargoTamano = match ($tamano) {
                    'mediano' => 1500,
                    'grande' => 3000,
                    default => 0,
                };
                $subtotal += $recargoTamano;
            }

            // Aplicar tarifa mínima
            if ($subtotal < self::DELIVERY_RATES['tarifa_minima']) {
                $subtotal = self::DELIVERY_RATES['tarifa_minima'];
            }

            // Descuento por distancia larga (más de 10 km)
            $descuento = 0;
            if ($distanciaKm > 10) {
                $descuento = $subtotal * 0.10; // 10% de descuento
            }

            $total = $subtotal - $descuento;

            return [
                'subtotal' => round($subtotal, 2),
                'descuento' => round($descuento, 2),
                'total' => round($total, 2),
                'detalles' => [
                    'tipo_domicilio' => $tipo->label(),
                    'distancia_km' => $distanciaKm,
                    'tarifa_base' => $tarifaBase,
                    'costo_por_km' => $costoPorKm,
                    'costo_distancia' => round($costoDistancia, 2),
                    'vehiculo' => $vehicle ? $vehicle->getFullName() : null,
                    'tamano_paquete' => $options['tamano'] ?? null,
                    'recargo_tamano' => isset($recargoTamano) ? $recargoTamano : 0,
                ]
            ];
        }, 'calcular_precio_domicilio');
    }

    /**
     * Calcular descuentos por duración
     */
    protected function calculateDiscount(float $subtotal, int $dias): float
    {
        // Sin descuento para menos de 3 días
        if ($dias < 3) {
            return 0;
        }

        // Descuentos progresivos
        if ($dias >= 7) {
            return $subtotal * 0.15; // 15% por semana o más
        } elseif ($dias >= 5) {
            return $subtotal * 0.10; // 10% por 5-6 días
        } else {
            return $subtotal * 0.05; // 5% por 3-4 días
        }
    }

    /**
     * Obtener estimación de precio rápida
     */
    public function getQuickEstimate(
        VehicleType $tipo,
        int $horas
    ): array {
        return $this->execute(function () use ($tipo, $horas) {
            $rates = self::BASE_RATES[$tipo->value];
            $dias = ceil($horas / 24);

            if ($horas <= 24) {
                $precio = $rates['hora'] * $horas;
            } else {
                $precio = $rates['dia'] * $dias;
            }

            $descuento = $this->calculateDiscount($precio, $dias);
            $total = $precio - $descuento;

            return [
                'tipo_vehiculo' => $tipo->label(),
                'horas' => $horas,
                'precio_estimado' => $this->formatCurrency($total),
                'precio_numerico' => round($total, 2),
            ];
        }, 'obtener_estimacion_rapida');
    }

    /**
     * Calcular costo por hora extra
     */
    public function calculateOvertimeCost(
        Vehicle $vehicle,
        int $minutosExtra
    ): array {
        return $this->execute(function () use ($vehicle, $minutosExtra) {
            $tipoVehiculo = $vehicle->tipo->value;
            $tarifaHora = self::BASE_RATES[$tipoVehiculo]['hora'];

            // Cobrar hora completa por cualquier fracción
            $horasExtra = ceil($minutosExtra / 60);

            // Recargo del 50% por tiempo extra
            $costoExtra = ($tarifaHora * $horasExtra) * 1.5;

            return [
                'minutos_extra' => $minutosExtra,
                'horas_cobradas' => $horasExtra,
                'tarifa_hora_normal' => $tarifaHora,
                'costo_extra' => round($costoExtra, 2),
                'descripcion' => "Tiempo extra: {$horasExtra} hora(s) con recargo del 50%"
            ];
        }, 'calcular_costo_tiempo_extra');
    }

    /**
     * Obtener tarifas de todos los vehículos
     */
    public function getAllVehicleRates(): array
    {
        return $this->execute(function () {
            $rates = [];

            foreach (VehicleType::cases() as $tipo) {
                $tipoValue = $tipo->value;
                $rates[$tipoValue] = [
                    'label' => $tipo->label(),
                    'tarifas' => self::BASE_RATES[$tipoValue],
                    'tarifas_formateadas' => [
                        'hora' => $this->formatCurrency(self::BASE_RATES[$tipoValue]['hora']),
                        'dia' => $this->formatCurrency(self::BASE_RATES[$tipoValue]['dia']),
                        'semana' => $this->formatCurrency(self::BASE_RATES[$tipoValue]['semana']),
                    ]
                ];
            }

            return $rates;
        }, 'obtener_todas_tarifas');
    }

    /**
     * Obtener tarifas de domicilios
     */
    public function getDeliveryRates(): array
    {
        return $this->execute(function () {
            return [
                'tarifa_base' => self::DELIVERY_RATES['tarifa_base'],
                'tarifa_base_formateada' => $this->formatCurrency(self::DELIVERY_RATES['tarifa_base']),
                'por_km_paquete' => self::DELIVERY_RATES['por_km_paquete'],
                'por_km_paquete_formateado' => $this->formatCurrency(self::DELIVERY_RATES['por_km_paquete']),
                'por_km_vehiculo' => self::DELIVERY_RATES['por_km_vehiculo'],
                'por_km_vehiculo_formateado' => $this->formatCurrency(self::DELIVERY_RATES['por_km_vehiculo']),
                'tarifa_minima' => self::DELIVERY_RATES['tarifa_minima'],
                'tarifa_minima_formateada' => $this->formatCurrency(self::DELIVERY_RATES['tarifa_minima']),
            ];
        }, 'obtener_tarifas_domicilio');
    }

    /**
     * Comparar precios entre tipos de vehículos
     */
    public function compareVehiclePrices(int $horas): array
    {
        return $this->execute(function () use ($horas) {
            $comparaciones = [];

            foreach (VehicleType::cases() as $tipo) {
                $estimacion = $this->getQuickEstimate($tipo, $horas);
                $comparaciones[] = [
                    'tipo' => $tipo->label(),
                    'precio' => $estimacion['data']['precio_numerico'],
                    'precio_formateado' => $estimacion['data']['precio_estimado'],
                ];
            }

            // Ordenar por precio
            usort($comparaciones, fn($a, $b) => $a['precio'] <=> $b['precio']);

            return [
                'horas' => $horas,
                'comparaciones' => $comparaciones,
                'mas_economico' => $comparaciones[0],
                'mas_costoso' => end($comparaciones),
            ];
        }, 'comparar_precios_vehiculos');
    }
}
