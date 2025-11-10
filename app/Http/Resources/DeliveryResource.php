<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
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
            
            // Tipo con info del enum
            'tipo' => [
                'value' => $this->tipo->value,
                'label' => $this->tipo->label(),
                'description' => $this->tipo->description(),
                'icon' => $this->tipo->icon(),
                'color' => $this->tipo->color(),
            ],
            
            // Descripción y peso (solo para paquetes)
            'descripcion' => $this->when($this->isPaquete(), $this->descripcion),
            'peso_kg' => $this->when($this->isPaquete(), (float) $this->peso_kg),
            
            // Direcciones con coordenadas
            'origen' => [
                'direccion' => $this->direccion_origen,
                'lat' => $this->lat_origen,
                'lon' => $this->lon_origen,
            ],
            
            'destino' => [
                'direccion' => $this->direccion_destino,
                'lat' => $this->lat_destino,
                'lon' => $this->lon_destino,
            ],
            
            // Distancia y tiempos
            'distancia_km' => $this->calcularDistancia(),
            'tiempo_estimado_minutos' => $this->calcularTiempoEstimado(),
            'tiempo_transcurrido_minutos' => $this->getTiempoTranscurrido(),
            
            // Costo
            'costo' => (float) $this->costo,
            'costo_formatted' => '$' . number_format($this->costo, 0, ',', '.'),
            
            // Estado (desde la reserva)
            'estado' => $this->getEstado(),
            
            // Fechas
            'fecha_entrega_estimada' => $this->fecha_entrega_estimada?->format('Y-m-d H:i:s'),
            'fecha_entrega_real' => $this->fecha_entrega_real?->format('Y-m-d H:i:s'),
            
            // Flags
            'is_paquete' => $this->isPaquete(),
            'is_vehiculo' => $this->isVehiculo(),
            'is_entregado' => $this->isEntregado(),
            'is_retrasado' => $this->isRetrasado(),
            
            // Relaciones
            'user' => $this->whenLoaded('user', function() {
                return new UserResource($this->user);
            }),
            
            'reservation' => $this->whenLoaded('reservation', function() {
                return new ReservationResource($this->reservation);
            }),
            
            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}