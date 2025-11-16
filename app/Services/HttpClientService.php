<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Exception;

/**
 * Servicio centralizado para llamadas HTTP externas con timeouts y retry logic
 */
class HttpClientService
{
    /**
     * Crear cliente HTTP con configuración de timeouts
     */
    public static function client(): PendingRequest
    {
        return Http::timeout(config('services.http.timeout', 30))
            ->connectTimeout(config('services.http.connect_timeout', 10))
            ->retry(
                times: config('services.http.retry.times', 3),
                sleep: config('services.http.retry.sleep', 1000),
                when: fn ($exception) => $exception instanceof \Illuminate\Http\Client\ConnectionException
            );
    }

    /**
     * Cliente HTTP para Mercado Pago
     */
    public static function mercadoPago(): PendingRequest
    {
        $accessToken = config('services.mercadopago.access_token');
        $timeout = config('services.mercadopago.timeout', 15);

        return Http::timeout($timeout)
            ->connectTimeout(10)
            ->withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ])
            ->retry(
                times: 2, // Solo 2 intentos para pagos
                sleep: 500,
                when: fn ($exception) => $exception instanceof \Illuminate\Http\Client\ConnectionException
            );
    }

    /**
     * Cliente HTTP para Azure IoT
     */
    public static function azureIoT(): PendingRequest
    {
        $timeout = config('services.azure_iot.timeout', 10);

        return Http::timeout($timeout)
            ->connectTimeout(5)
            ->retry(
                times: 3,
                sleep: 1000,
                when: fn ($exception) => $exception instanceof \Illuminate\Http\Client\ConnectionException
            );
    }

    /**
     * Ejecutar request con manejo de errores y logging
     */
    public static function safeRequest(callable $callback, string $context = 'HTTP Request'): array
    {
        try {
            $response = $callback();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'status' => $response->status(),
                ];
            }

            \Log::warning("[{$context}] Request failed", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => "Error en {$context}: " . $response->status(),
                'status' => $response->status(),
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::error("[{$context}] Connection timeout", [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => "Timeout al conectar con {$context}",
                'error' => 'connection_timeout',
            ];

        } catch (\Illuminate\Http\Client\RequestException $e) {
            \Log::error("[{$context}] Request exception", [
                'error' => $e->getMessage(),
                'status' => $e->response?->status(),
            ]);

            return [
                'success' => false,
                'message' => "Error en solicitud a {$context}",
                'error' => 'request_failed',
            ];

        } catch (Exception $e) {
            \Log::error("[{$context}] Unexpected error", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => "Error inesperado en {$context}",
                'error' => 'unexpected_error',
            ];
        }
    }
}
