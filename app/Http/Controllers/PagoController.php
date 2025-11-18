<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;

class PagoController extends Controller
{
    public function pagar()
    {
        try {
            // ============================
            // 1. CONFIGURAR ACCESS TOKEN
            // ============================
            $token = env('MERCADOPAGO_ACCESS_TOKEN');

            if (!$token || $token === '') {
                return back()->with('error', 'El Access Token de MercadoPago no está configurado.');
            }

            MercadoPagoConfig::setAccessToken($token);

            // ============================
            // 2. CREAR CLIENTE
            // ============================
            $client = new PreferenceClient();

            // ============================
            // 3. CREAR PREFERENCIA REAL
            // ============================
            $preference = $client->create([
                "items" => [
                    [
                        "title" => "Reserva BgaGO",
                        "quantity" => 1,
                        "unit_price" => 10000,
                        "currency_id" => "COP"
                    ]
                ],

                "back_urls" => [
                    "success" => url('/pago-exitoso'),
                    "failure" => url('/pago-fallido'),
                    "pending" => url('/pago-pendiente'),
                ],

                "auto_return" => "approved",
            ]);

            // ============================
            // 4. VALIDAR Y REDIRIGIR
            // ============================
            if (!isset($preference->init_point)) {
                Log::error("MercadoPago no devolvió init_point", [
                    'preference' => $preference
                ]);

                return back()->with('error', 'Error inesperado al crear la preferencia.');
            }

            return redirect()->away($preference->init_point);
        }

        // ============================
        // 5. ERRORES ESPECÍFICOS MP
        // ============================
        catch (MPApiException $e) {
            Log::error("ERROR MercadoPago API", [
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
                'response' => $e->getApiResponse(), // <-- AQUÍ ESTÁ LA VERDAD
            ]);

            return back()->with('error', 'Error al comunicarse con MercadoPago: revisa el log.');
        }

        // ============================
        // 6. ERRORES GENERALES
        // ============================
        catch (\Exception $e) {
            Log::error("ERROR GENERAL en Pago", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Ocurrió un error inesperado al procesar el pago.');
        }
    }
}
