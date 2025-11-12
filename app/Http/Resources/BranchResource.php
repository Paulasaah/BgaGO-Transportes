<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            
            // Coordenadas
            'location' => [
                'lat' => (float) $this->lat,
                'lon' => (float) $this->lon,
            ],
            
            // Radio de cobertura
            'radio' => $this->radio,
            'color' => $this->color,
            'descripcion' => $this->descripcion,
            
            // Capacidad
            'capacidad_vehiculos' => $this->capacidad_vehiculos,
            'vehiculos_actuales' => $this->whenCounted('vehicles'),
            'ocupacion_porcentaje' => $this->when(
                $request->input('include_stats'),
                $this->getOccupancyPercentage()
            ),
            
            // Vehículos disponibles
            'vehiculos_disponibles' => $this->when(
                $request->input('include_vehicles'),
                function() {
                    return VehicleResource::collection(
                        $this->vehicles()->disponibles()->get()
                    );
                }
            ),
            
            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}