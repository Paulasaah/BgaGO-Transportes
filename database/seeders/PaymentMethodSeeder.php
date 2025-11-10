<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $metodos = [
            [
                'nombre' => 'Pago en Efectivo',
                'tipo' => 'efectivo',
                'activo' => true,
                'descripcion' => 'El cliente paga directamente en la sede o al recibir el vehículo.',
                'config' => null,
            ],
            [
                'nombre' => 'Transferencia Bancaria',
                'tipo' => 'transferencia',
                'activo' => true,
                'descripcion' => 'Pago mediante transferencia a cuenta bancaria verificada.',
                'config' => [
                    'banco' => 'Bancolombia',
                    'numero_cuenta' => '1234567890',
                    'tipo_cuenta' => 'Ahorros',
                ],
            ],
            [
                'nombre' => 'Tarjeta de Crédito o Débito',
                'tipo' => 'tarjeta',
                'activo' => true,
                'descripcion' => 'Pago con tarjeta a través de terminal o pasarela segura.',
                'config' => [
                    'proveedor' => 'Datáfono Redeban',
                    'moneda' => 'COP',
                ],
            ],
            [
                'nombre' => 'MercadoPago',
                'tipo' => 'mercadopago',
                'activo' => true,
                'descripcion' => 'Pago en línea con integración a la API oficial de MercadoPago.',
                'config' => [
                    'sandbox' => true,
                    'public_key' => 'TEST-1234567890',
                    'access_token' => 'TEST-ACCESS-TOKEN',
                    'moneda' => 'COP',
                ],
            ],
        ];

        foreach ($metodos as $m) {
            PaymentMethod::create($m);
        }
    }
}
