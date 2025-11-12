<?php

namespace App\Http\Controllers\Admin\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

trait InteractsWithApiController
{
    /**
     * Ejecuta un método del API Controller y convierte la respuesta JSON en redirect.
     * 
     * @param string $apiControllerClass Clase del controlador API (e.g. \App\Http\Controllers\Api\ReservationController::class)
     * @param string $method Nombre del método a ejecutar
     * @param array $parameters Parámetros a pasar al método
     * @param string|null $successRoute Ruta a redirigir en caso de éxito (null = back)
     * @param string $successMessage Mensaje flash de éxito
     * @return RedirectResponse
     */
    protected function delegateToApi(
        string $apiControllerClass,
        string $method,
        array $parameters = [],
        string $successRoute = null,
        string $successMessage = 'Operación exitosa'
    ): RedirectResponse {
        try {
            $apiController = app($apiControllerClass);

            // Ejecutar método en el API controller
            $response = $apiController->$method(...$parameters);

            // Si devuelve JsonResponse, interpretamos el resultado
            if ($response instanceof JsonResponse) {
                $data = $response->getData(true);
                $statusCode = $response->getStatusCode();

                if ($statusCode >= 200 && $statusCode < 300) {
                    return $successRoute
                        ? redirect()->route($successRoute)->with('success', $successMessage)
                        : back()->with('success', $successMessage);
                }

                // Si hubo error desde el API
                $errorMessage = $data['message'] ?? 'Error en la operación';
                $errors = $data['errors'] ?? [];

                return back()
                    ->withInput()
                    ->with('error', $errorMessage)
                    ->withErrors($errors);
            }

            // Si no es JsonResponse, devolver tal cual
            return $response;

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Error de validación');

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return back()->with('error', 'No tienes permiso para realizar esta acción');

        } catch (\Exception $e) {
            \Log::error("Error delegando a API: {$e->getMessage()}", [
                'controller' => $apiControllerClass,
                'method' => $method,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    /**
     * Shortcut para operaciones de creación
     */
    protected function createViaApi(
        string $apiControllerClass,
        $request,
        string $indexRoute
    ): RedirectResponse {
        return $this->delegateToApi(
            $apiControllerClass,
            'store',
            [$request],
            $indexRoute,
            'Registro creado exitosamente'
        );
    }

    /**
     * Shortcut para operaciones de actualización
     */
    protected function updateViaApi(
        string $apiControllerClass,
        $request,
        $model,
        string $showRoute = null
    ): RedirectResponse {
        return $this->delegateToApi(
            $apiControllerClass,
            'update',
            [$request, $model],
            $showRoute,
            'Registro actualizado exitosamente'
        );
    }

    /**
     * Shortcut para operaciones de eliminación
     */
    protected function deleteViaApi(
        string $apiControllerClass,
        $model,
        string $indexRoute
    ): RedirectResponse {
        return $this->delegateToApi(
            $apiControllerClass,
            'destroy',
            [$model],
            $indexRoute,
            'Registro eliminado exitosamente'
        );
    }
}
