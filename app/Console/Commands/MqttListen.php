<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Services\TelemetryProcessingService;
use Illuminate\Support\Facades\Log;

/**
 * Command: Escuchar mensajes MQTT de telemetría
 * 
 * Responsabilidad: Solo conectar a MQTT y delegar procesamiento al Service
 * Sigue principio SOLID: Single Responsibility
 */
class MqttListen extends Command
{
    protected $signature = 'mqtt:listen 
                            {--host= : MQTT broker host}
                            {--port= : MQTT broker port}
                            {--topic=vehiculos/+/telemetria : MQTT topic pattern}';
    
    protected $description = 'Escucha mensajes MQTT de telemetría vehicular y los procesa en tiempo real';

    private TelemetryProcessingService $telemetryService;
    private int $messagesProcessed = 0;
    private int $messagesFailed = 0;

    public function __construct(TelemetryProcessingService $telemetryService)
    {
        parent::__construct();
        $this->telemetryService = $telemetryService;
    }

    public function handle(): int
    {
        $host = $this->option('host') ?: config('mqtt-client.connections.local.host', 'mosquitto');
        $port = (int) ($this->option('port') ?: config('mqtt-client.connections.local.port', 1883));
        $topic = $this->option('topic');
        $clientId = 'laravel-listener-' . uniqid();

        $this->info("🚀 Iniciando listener MQTT");
        $this->info("📡 Broker: {$host}:{$port}");
        $this->info("📢 Topic: {$topic}");
        $this->newLine();

        try {
            // Configurar conexión MQTT
            $settings = (new ConnectionSettings())
                ->setKeepAliveInterval(60)
                ->setConnectTimeout(5)
                ->setUseTls(false);
            
            // Solo configurar username/password si están definidos
            $username = config('mqtt-client.connections.local.username');
            $password = config('mqtt-client.connections.local.password');
            
            if ($username !== null && $username !== '') {
                $settings->setUsername($username);
            }
            
            if ($password !== null && $password !== '') {
                $settings->setPassword($password);
            }

            $mqtt = new MqttClient($host, $port, $clientId);
            
            // Conectar con clean session
            $mqtt->connect($settings, true);
            
            $this->info('✅ Conectado al broker MQTT');

            // Suscribirse al topic con QoS 1 (entrega garantizada)
            $mqtt->subscribe($topic, function (string $topic, string $message) {
                $this->info("🔔 Mensaje recibido en topic: {$topic}");
                $this->line("📦 Tamaño: " . strlen($message) . " bytes");
                $this->processMessage($topic, $message);
            }, 1);

            $this->info('✅ Suscrito al topic: ' . $topic);
            $this->newLine();
            $this->info('📊 Esperando mensajes... (Ctrl+C para detener)');
            $this->newLine();

            // Loop infinito para escuchar mensajes
            $mqtt->loop(true);

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $this->error('❌ Error fatal en MQTT listener: ' . $e->getMessage());
            Log::error('MQTT listener crashed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return Command::FAILURE;
        }
    }

    /**
     * Procesar mensaje MQTT recibido
     * Delega al Service para mantener responsabilidad única
     */
    private function processMessage(string $topic, string $message): void
    {
        try {
            $this->line("🔄 Procesando mensaje...");
            
            // Delegar procesamiento al Service
            $success = $this->telemetryService->processMqttMessage($topic, $message);

            if ($success) {
                $this->messagesProcessed++;
                
                // Extraer device_id del mensaje para logging
                $data = json_decode($message, true);
                $deviceId = $data['device_id'] ?? 'unknown';
                
                $this->line("✅ [{$this->messagesProcessed}] {$deviceId} - " . now()->format('H:i:s'));
                
                // Mostrar estadísticas cada 10 mensajes
                if ($this->messagesProcessed % 10 === 0) {
                    $this->showStats();
                }
            } else {
                $this->messagesFailed++;
                $this->error("❌ Procesamiento falló");
            }

        } catch (\Throwable $e) {
            $this->messagesFailed++;
            $this->error("⚠️ Error procesando mensaje: {$e->getMessage()}");
            $this->error("Stack trace: " . $e->getTraceAsString());
            
            Log::error('Error procesando mensaje MQTT', [
                'topic' => $topic,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Mostrar estadísticas de procesamiento
     */
    private function showStats(): void
    {
        $this->newLine();
        $this->info('📊 Estadísticas:');
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Mensajes procesados', $this->messagesProcessed],
                ['Mensajes fallidos', $this->messagesFailed],
                ['Tasa de éxito', $this->getSuccessRate() . '%'],
                ['Uptime', $this->getUptime()],
            ]
        );
        $this->newLine();
    }

    /**
     * Calcular tasa de éxito
     */
    private function getSuccessRate(): string
    {
        $total = $this->messagesProcessed + $this->messagesFailed;
        if ($total === 0) {
            return '0.00';
        }
        return number_format(($this->messagesProcessed / $total) * 100, 2);
    }

    /**
     * Obtener tiempo de ejecución
     */
    private function getUptime(): string
    {
        // Implementación simple, se puede mejorar guardando el tiempo de inicio
        return 'N/A';
    }
}
