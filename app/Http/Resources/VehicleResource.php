<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            'placa' => $this->placa,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'year' => $this->year,
            'color' => $this->color,
            
            // Tipo y estado con info adicional del enum
            'tipo' => [
                'value' => $this->tipo->value,
                'label' => $this->tipo->label(),
                'icon' => $this->tipo->icon(),
                'color' => $this->tipo->color(),
            ],
            
            'estado' => [
                'value' => $this->estado->value,
                'label' => $this->estado->label(),
                'icon' => $this->estado->icon(),
                'color' => $this->estado->color(),
                'is_available' => $this->estado->isAvailable(),
            ],
            
            // Precios
            'precio_hora' => (float) $this->precio_hora,
            'precio_dia' => (float) $this->precio_dia,
            'precio_hora_formatted' => '$' . number_format($this->precio_hora, 0, ',', '.'),
            'precio_dia_formatted' => '$' . number_format($this->precio_dia, 0, ',', '.'),
            
            // Disponibilidad
            'visible_catalogo' => $this->visible_catalogo,
            'is_disponible' => $this->isDisponible(),
            'has_driver' => $this->hasDriver(),
            
            // Multimedia
            'imagen_principal' => $this->imagen_principal ? asset('storage/' . $this->imagen_principal) : null,
            'descripcion' => $this->descripcion,
            
            // Nombre completo
            'full_name' => $this->getFullName(),
            
            // Estadísticas (solo si se solicita)
            'stats' => $this->when($request->input('include_stats'), [
                'total_ingresos' => $this->getTotalIngresos(),
                'average_rating' => $this->getAverageRating(),
            ]),
            
            // Relaciones
            'branch' => $this->whenLoaded('branch', function() {
                return new BranchResource($this->branch);
            }),
            
            'driver' => $this->whenLoaded('driver', function() {
                return new UserResource($this->driver);
            }),
            
            // Ubicación actual (telemetría)
            'current_location' => $this->when(
                $request->input('include_location'),
                $this->getCurrentLocation()
            ),
            
            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}