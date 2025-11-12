<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuracion;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            [
                'clave' => 'mercadopago_public_key',
                'valor' => 'TEST-1234567890',
                'descripcion' => 'Public key de MercadoPago (modo sandbox)',
            ],
            [
                'clave' => 'mercadopago_access_token',
                'valor' => 'TEST-ACCESS-TOKEN',
                'descripcion' => 'Access token para integración de MercadoPago.',
            ],
            [
                'clave' => 'mercadopago_mode',
                'valor' => 'sandbox',
                'descripcion' => 'Modo actual de la integración (sandbox o live).',
            ],
            [
                'clave' => 'empresa_email_contacto',
                'valor' => 'contacto@empresa.com',
                'descripcion' => 'Correo general de contacto.',
            ],
            [
                'clave' => 'empresa_telefono_contacto',
                'valor' => '+57 607 1234567',
                'descripcion' => 'Teléfono principal de atención al cliente.',
            ],
        ];

        foreach ($configs as $config) {
            Configuracion::updateOrCreate(['clave' => $config['clave']], $config);
        }

        $this->command->info('✅ Configuraciones creadas o actualizadas correctamente.');
    }
}
