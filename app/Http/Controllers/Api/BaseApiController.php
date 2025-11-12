<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class BaseApiController extends Controller
{
    /**
     * Respuesta exitosa
     */
    protected function success($data = null, string $message = 'Operación exitosa', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Respuesta de error
     */
    protected function error(string $message, $errors = null, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Respuesta de recurso creado
     */
    protected function created($data, string $message = 'Recurso creado exitosamente'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    /**
     * Respuesta de recurso no encontrado
     */
    protected function notFound(string $message = 'Recurso no encontrado'): JsonResponse
    {
        return $this->error($message, null, 404);
    }

    /**
     * Respuesta de validación fallida
     */
    protected function validationError(string $message, $errors = null): JsonResponse
    {
        return $this->error($message, $errors, 422);
    }

    /**
     * Respuesta no autorizado
     */
    protected function unauthorized(string $message = 'No autorizado'): JsonResponse
    {
        return $this->error($message, null, 403);
    }

    /**
     * Respuesta de error del servidor
     */
    protected function serverError(string $message = 'Error interno del servidor'): JsonResponse
    {
        return $this->error($message, null, 500);
    }

    /**
     * Procesar resultado de servicio
     */
    protected function handleServiceResult(array $result, string $successMessage = 'Operación exitosa'): JsonResponse
    {
        if ($result['success']) {
            return $this->success($result['data'], $result['message'] ?? $successMessage);
        }

        return $this->error($result['message'], $result['errors'] ?? null);
    }
}