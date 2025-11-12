<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Models\Telemetria;

class MqttListen extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Escucha los mensajes MQTT y guarda la telemetría en la base de datos.';

    public function handle()
    {
        $server   = '127.0.0.1';
        $port     = 1883;
        $clientId = 'laravel-listener-' . uniqid();

        $settings = (new ConnectionSettings())
            ->setUsername(null)
            ->setPassword(null)
            ->setKeepAliveInterval(60);

        $mqtt = new MqttClient($server, $port, $clientId);

        $mqtt->connect($settings, true);

        // Escuchar todos los vehículos
        $mqtt->subscribe('vehiculos/+/telemetria', function (string $topic, string $message) {
            $this->processMessage($topic, $message);
        }, 0);

        $this->info('📡 Escuchando mensajes MQTT...');
        $mqtt->loop(true);
    }

    private function processMessage(string $topic, string $message)
    {
        try {
            $data = json_decode($message, true);

            if (!$data || !isset($data['device_id'])) {
                $this->warn('⚠️ Mensaje MQTT inválido: ' . $message);
                return;
            }

            // Guardar en la base de datos
            Telemetria::create([
                'device_id' => $data['device_id'],
                'lat'       => $data['Geopoint']['lat'] ?? 0,
                'lon'       => $data['Geopoint']['lon'] ?? 0,
                'battery'   => $data['Battery'] ?? null,
            ]);

            $this->info('✅ Telemetría recibida de ' . $data['device_id']);
        } catch (\Throwable $e) {
            $this->error('❌ Error procesando mensaje: ' . $e->getMessage());
        }
    }
}
