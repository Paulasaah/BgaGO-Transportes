<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IotDataController extends Controller
{
    public function sendTelemetry(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string',
            'Geopoint.lat' => 'required|numeric',
            'Geopoint.lon' => 'required|numeric',
            'Battery' => 'required|numeric',
        ]);

        $deviceId = $validated['device_id'];
        $base = config('azure.iot_base');
        $token = config('azure.iot_token');
        $apiVersion = '2022-10-31-preview';

        $endpoint = "{$base}/api/devices/{$deviceId}/telemetry?api-version={$apiVersion}";

        $response = Http::withHeaders([
            'Authorization' => $token,
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            "Geopoint" => $validated['Geopoint'],
            "Battery"  => $validated['Battery'],
        ]);

        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'status'  => $response->status(),
            ]);
        }

        return response()->json([
            'success'  => false,
            'status'   => $response->status(),
            'response' => $response->body(),
        ]);
    }
}
