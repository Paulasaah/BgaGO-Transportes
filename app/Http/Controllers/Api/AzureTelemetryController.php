<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class AzureTelemetryController extends Controller
{
    public function getDeviceTelemetry($deviceId)
    {
        try {
            $base = config('azure.iot_base');
            $token = config('azure.iot_token');

            // Detectar automáticamente el template correcto
            $templateResponse = Http::withHeaders([
                'Authorization' => $token,
                'Content-Type' => 'application/json',
            ])->get("{$base}/api/deviceTemplates?api-version=2022-07-31");

            $templateData = $templateResponse->json();
            $templateId = $templateData['value'][0]['@id'] ?? 'dtmi:cmv8b5qh:z5orxso6';

            // 🔹 CORREGIDO: usar variables como texto literal
            $query = [
                'query' => "SELECT \$id, \$ts, Geopoint.lat AS lat, Geopoint.lon AS lon, Battery 
                            FROM {$templateId} 
                            WHERE \$id = '{$deviceId}' AND WITHIN_WINDOW(PT10M)"
            ];


            $endpoint = "{$base}/api/query?api-version=2022-06-30-preview";

            $response = Http::withHeaders([
                'Authorization' => $token,
                'Content-Type' => 'application/json',
            ])->post($endpoint, $query);

            Log::info('🔍 Azure IoT Central Query', [
                'status' => $response->status(),
                'query' => $query,
                'response' => $response->json(),
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'device_id' => $deviceId,
                    'data' => $response->json(),
                ]);
            }

            return response()->json([
                'error' => '❌ Error al consultar Azure IoT Central.',
                'status' => $response->status(),
                'body' => $response->body(),
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('⚠️ Excepción en AzureTelemetryController', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'Excepción: ' . $e->getMessage()], 500);
        }
    }

}
