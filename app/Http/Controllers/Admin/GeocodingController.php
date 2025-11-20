<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\GeocodingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeocodingController extends Controller
{
    public function __construct(protected GeocodingService $geocodingService)
    {
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|max:255',
        ]);

        $query = $request->input('q');

        $result = $this->geocodingService->search($query, 5);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'results' => [],
                'message' => $result['message'] ?? 'Error de geocodificación',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'results' => $result['results'],
        ]);
    }
}
