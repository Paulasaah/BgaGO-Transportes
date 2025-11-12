<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'license_number' => $this->license_number,
            'rating' => (float) $this->rating,
            'is_active' => $this->is_active,
            
            // Usuario asociado
            'user' => $this->whenLoaded('user', function() {
                return new UserResource($this->user);
            }),
            
            // Estadísticas (opcional)
            'stats' => $this->when($request->input('include_stats'), function() {
                return [
                    'total_deliveries' => $this->reservations()->where('tipo', 'domicilio')->count(),
                    'completed_deliveries' => $this->reservations()
                        ->where('tipo', 'domicilio')
                        ->where('estado', 'completada')
                        ->count(),
                    'total_earnings' => $this->reservations()
                        ->where('estado', 'completada')
                        ->sum('monto_final'),
                ];
            }),
            
            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}