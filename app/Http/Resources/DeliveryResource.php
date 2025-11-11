<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class DeliveryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $delivery = $this->resource;

        // Protección ante objetos inesperados
        if (!method_exists($delivery, 'isPaquete')) {
            return [
                'id' => $delivery->id ?? null,
                'warning' => 'El recurso no es una instancia de Delivery',
                'type' => get_class($delivery),
            ];
        }

        return [
            'id' => $delivery->id,

            // Enum tipo (Paquete o Vehículo)
            'tipo' => [
                'value' => $delivery->tipo->value,
                'label' => $delivery->tipo->label(),
                'description' => $delivery->tipo->description(),
                'icon' => $delivery->tipo->icon(),
                'color' => $delivery->tipo->color(),
            ],

            // Descripción y peso (solo si es paquete)
            'descripcion_contenido' => $this->when($delivery->isPaquete(), $delivery->descripcion_contenido),
            'peso_estimado' => $this->when($delivery->isPaquete(), (float) $delivery->peso_estimado),

            // Coordenadas (obtenidas de la reserva)
            'origen' => $delivery->getOrigen(),
            'destino' => $delivery->getDestino(),

            // Distancia y tiempos
            'distancia_km' => $delivery->getDistanciaKm(),
            'tiempo_estimado_minutos' => $delivery->calcularTiempoEstimado(),
            'tiempo_transcurrido_minutos' => $delivery->getTiempoTranscurrido(),

            // Costo
            'costo' => (float) $delivery->costo,
            'costo_formatted' => '$' . number_format($delivery->costo, 0, ',', '.'),

            // Estado (sin riesgo de null)
            'estado' => $delivery->getEstado(),

            // Fechas
            'fecha_entrega_estimada' => $delivery->fecha_entrega_estimada?->format('Y-m-d H:i:s'),
            'fecha_entrega_real' => $delivery->fecha_entrega_real?->format('Y-m-d H:i:s'),

            // Flags
            'is_paquete' => $delivery->isPaquete(),
            'is_vehiculo' => $delivery->isVehiculo(),
            'is_entregado' => $delivery->isEntregado(),
            'is_retrasado' => $delivery->isRetrasado(),

            // Relaciones (limpias, sin loops)
            'user' => $this->whenLoaded('user', fn() => new UserResource($delivery->user)),
            'reservation' => $this->whenLoaded('reservation', fn() => new ReservationResource($delivery->reservation->withoutRelations())),

            // Fecha de creación/actualización
            'created_at' => $delivery->created_at? Carbon::parse($this->created_at)->format('Y-m-d H:i:s') : null,
            'created_at_iso' => $delivery->created_at?->toIso8601String(),
            'updated_at' => $delivery->updated_at? Carbon::parse($this->updated_at)->format('Y-m-d H:i:s') : null,
            'updated_at_iso' => $delivery->updated_at?->toIso8601String(),

            // Días transcurridos desde creación (para métricas)
            'dias_transcurridos' => $delivery->created_at
                ? $delivery->created_at->diffInDays(Carbon::now())
                : null,
        ];
    }
}
