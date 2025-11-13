<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Clase base para todos los servicios
 * Proporciona helpers comunes y manejo de errores
 */
abstract class BaseService
{
    /**
     * Ejecutar operación con transacción y logs
     */
    protected function executeWithTransaction(callable $callback, string $operationName)
    {
        try {
            DB::beginTransaction();
            
            $result = $callback();
            
            DB::commit();
            
            $this->logSuccess($operationName, $result);
            
            return [
                'success' => true,
                'data' => $result,
                'message' => "Operación '{$operationName}' completada exitosamente"
            ];
            
        } catch (Exception $e) {
            DB::rollBack();
            
            $this->logError($operationName, $e);
            
            return [
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e
            ];
        }
    }

    /**
     * Ejecutar sin transacción (para consultas)
     */
    protected function execute(callable $callback, string $operationName)
    {
        try {
            $result = $callback();
            
            return [
                'success' => true,
                'data' => $result,
                'message' => "Consulta '{$operationName}' exitosa"
            ];
            
        } catch (Exception $e) {
            $this->logError($operationName, $e);
            
            return [
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e
            ];
        }
    }

    /**
     * Validar datos requeridos
     */
    protected function validateRequired(array $data, array $requiredFields): bool
    {
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new Exception("El campo '{$field}' es requerido");
            }
        }
        
        return true;
    }

    /**
     * Log de éxito
     */
    protected function logSuccess(string $operation, $data = null): void
    {
        Log::info("✅ {$operation}", [
            'service' => static::class,
            'user_id' => auth()->id(),
            'data' => $data,
            'timestamp' => now()
        ]);
    }

    /**
     * Log de error
     */
    protected function logError(string $operation, Exception $e): void
    {
        Log::error("❌ {$operation}", [
            'service' => static::class,
            'user_id' => auth()->id(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'timestamp' => now()
        ]);
    }

    /**
     * Formatear respuesta de éxito
     */
    protected function success($data = null, string $message = 'Operación exitosa'): array
    {
        return [
            'success' => true,
            'data' => $data,
            'message' => $message
        ];
    }

    /**
     * Formatear respuesta de error
     */
    protected function error(string $message, $data = null): array
    {
        return [
            'success' => false,
            'data' => $data,
            'message' => $message
        ];
    }

    /**
     * Calcular distancia entre dos puntos GPS (Haversine)
     */
    protected function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Calcular tiempo estimado (15 km/h promedio en ciudad)
     */
    protected function calculateEstimatedTime(float $distanceKm): int
    {
        $averageSpeed = 15; // km/h
        $hours = $distanceKm / $averageSpeed;
        return (int) ceil($hours * 60); // minutos
    }

    /**
     * Generar código único
     */
    protected function generateUniqueCode(string $prefix, int $length = 6): string
    {
        $timestamp = now()->format('ymd');
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, $length));
        
        return "{$prefix}-{$timestamp}-{$random}";
    }

    /**
     * Formatear moneda COP
     */
    protected function formatCurrency(float $amount): string
    {
        return '$' . number_format($amount, 0, ',', '.');
    }

    /**
     * Parsear coordenadas desde string
     */
    protected function parseCoordinates(string $coordinates): ?array
    {
        $parts = explode(',', $coordinates);
        
        if (count($parts) !== 2) {
            return null;
        }
        
        return [
            'lat' => (float) trim($parts[0]),
            'lng' => (float) trim($parts[1])
        ];
    }
}