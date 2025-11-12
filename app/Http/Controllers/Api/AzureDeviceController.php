<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class AzureDeviceController extends Controller
{
    public function registerDevice($deviceId)
    {
        try {
            $base = config('azure.iot_base');
            $token = config('azure.iot_token');

            // Consultar automáticamente el template más reciente
            $templateResponse = Http::withHeaders([
                'Authorization' => $token,
                'Content-Type' => 'application/json',
            ])->get("{$base}/api/deviceTemplates?api-version=2022-07-31");

            $templateData = $templateResponse->json();
            $templateId = $templateData['value'][0]['@id'] ?? 'dtmi:cmv8b5qh:z5orxso6';

            $endpoint = "{$base}/api/devices/{$deviceId}?api-version=2022-07-31";

            $payload = [
                "displayName" => ucfirst($deviceId),
                "template" => $templateId,
                "enabled" => true,
            ];

            $response = Http::withHeaders([
                'Authorization' => $token,
                'Content-Type' => 'application/json',
            ])->put($endpoint, $payload);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'device_id' => $deviceId,
                    'azure_response' => $response->json(),
                ]);
            }

            Log::error('❌ Error al registrar dispositivo.', ['body' => $response->body()]);

            return response()->json([
                'success' => false,
                'error' => '❌ Error al registrar dispositivo.',
                'status' => $response->status(),
                'response' => $response->body(),
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('⚠️ Excepción en AzureDeviceController', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'Excepción: ' . $e->getMessage()], 500);
        }
    }
}
