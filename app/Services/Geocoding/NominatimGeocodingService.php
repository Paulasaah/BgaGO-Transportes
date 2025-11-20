<?php

namespace App\Services\Geocoding;

use App\Contracts\GeocodingService;
use Illuminate\Support\Facades\Http;

class NominatimGeocodingService implements GeocodingService
{
    public function geocode(string $query): array
    {
        $results = $this->search($query, 1);

        if (!$results['success'] || empty($results['results'])) {
            return [
                'success' => false,
                'lat' => null,
                'lng' => null,
                'address' => null,
                'raw' => null,
                'message' => $results['message'] ?? 'Sin resultados',
            ];
        }

        $first = $results['results'][0];

        return [
            'success' => true,
            'lat' => $first['lat'],
            'lng' => $first['lng'],
            'address' => $first['label'],
            'raw' => $first['raw'],
            'message' => null,
        ];
    }

    public function search(string $query, int $limit = 5): array
    {
        $config = config('geocoding.providers.nominatim', []);
        $baseUrl = rtrim($config['base_url'] ?? '', '/');

        if ($baseUrl === '') {
            return [
                'success' => false,
                'results' => [],
                'message' => 'Configuración de Nominatim incompleta',
            ];
        }

        try {
            $timeout = (int) ($config['timeout'] ?? 5);
            $email = $config['email'] ?? null;

            $userAgent = 'BgaGO-Geocoder';
            if ($email) {
                $userAgent .= ' ' . $email;
            }

            $headers = [
                'User-Agent' => $userAgent,
                'Accept' => 'application/json',
            ];

            $params = [
                'q' => $query,
                'format' => 'jsonv2',
                'limit' => $limit,
                'addressdetails' => 1,
                'countrycodes' => $config['countrycodes'] ?? 'co',
            ];

            if (!empty($config['viewbox'])) {
                $params['bounded'] = 1;
                $params['viewbox'] = $config['viewbox'];
            }

            $response = Http::timeout($timeout)
                ->withHeaders($headers)
                ->get($baseUrl . '/search', $params);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'results' => [],
                    'message' => 'Error de geocodificación',
                ];
            }

            $rawResults = $response->json();

            if (empty($rawResults)) {
                return [
                    'success' => false,
                    'results' => [],
                    'message' => 'Sin resultados',
                ];
            }

            $results = collect($rawResults)->map(function ($item) use ($query) {
                return [
                    'label' => $item['display_name'] ?? $query,
                    'lat' => isset($item['lat']) ? (float) $item['lat'] : null,
                    'lng' => isset($item['lon']) ? (float) $item['lon'] : null,
                    'raw' => $item,
                ];
            })->all();

            return [
                'success' => true,
                'results' => $results,
                'message' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'results' => [],
                'message' => $e->getMessage(),
            ];
        }
    }
}
