<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ReservationResource extends JsonResource
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
            'codigo' => $this->codigo,
            
            // Tipo y estado con info del enum
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
                'is_active' => $this->estado->isActive(),
                'is_final' => $this->estado->isFinal(),
                'can_be_cancelled' => $this->estado->canBeCancelled(),
            ],
            
            // Direcciones
            'origen' => [
                'direccion' => $this->origen_direccion,
                'lat' => $this->origen_lat,
                'lng' => $this->origen_lng,
            ],
            
            'destino' => [
                'direccion' => $this->destino_direccion,
                'lat' => $this->destino_lat,
                'lng' => $this->destino_lng,
            ],
            
            // Fechas
            'fecha_inicio' => $this->fecha_inicio->format('Y-m-d H:i:s'),
            'fecha_fin' => $this->fecha_fin->format('Y-m-d H:i:s'),
            'fecha_confirmacion' => $this->fecha_confirmacion?->format('Y-m-d H:i:s'),
            'fecha_inicio_real' => $this->fecha_inicio_real?->format('Y-m-d H:i:s'),
            'fecha_fin_real' => $this->fecha_fin_real?->format('Y-m-d H:i:s'),
            
            // Duraciones
            'duracion_estimada' => $this->getDuracionEstimada(),
            'duracion_real' => $this->getDuracionReal(),
            
            // Precios
            'monto' => (float) $this->monto,
            'descuento' => (float) $this->descuento,
            'monto_final' => (float) $this->monto_final,
            'monto_formatted' => '$' . number_format($this->monto, 0, ',', '.'),
            'monto_final_formatted' => '$' . number_format($this->monto_final, 0, ',', '.'),
            
            // Distancia y duración
            'distancia_km' => $this->distancia_km,
            'duracion_minutos' => $this->duracion_minutos,
            
            // Calificaciones
            'calificacion_conductor' => $this->calificacion_conductor,
            'comentario_conductor' => $this->comentario_conductor,
            'calificacion_cliente' => $this->calificacion_cliente,
            'comentario_cliente' => $this->comentario_cliente,
            
            // Notas
            'notas_cliente' => $this->notas_cliente,
            'notas_conductor' => $this->when(
                $request->user()?->id === $this->conductor_id || $request->user()?->hasRole('admin'),
                $this->notas_conductor
            ),
            'notas_admin' => $this->when(
                $request->user()?->hasRole('admin'),
                $this->notas_admin
            ),
            
            // Cancelación
            'motivo_cancelacion' => $this->motivo_cancelacion,
            'notas_cliente' => $this->notas_cliente,
            'notas_conductor' => $this->when(
                $request->user()?->id === $this->conductor_id || $request->user()?->hasRole('admin'),
                $this->notas_conductor
            ),
            'notas_admin' => $this->when(
                $request->user()?->hasRole('admin'),
                $this->notas_admin
            ),
            
            // Flags útiles
            'is_reserva' => $this->isReserva(),
            'is_domicilio' => $this->isDomicilio(),
            'has_paid_payment' => $this->hasPaidPayment(),
            
            // Relaciones básicas
            'user' => $this->whenLoaded('user', function() {
                return new UserResource($this->user);
            }),
            
            'vehicle' => $this->whenLoaded('vehicle', function() {
                return new VehicleResource($this->vehicle);
            }),
            
            'driver' => $this->whenLoaded('driver', function() {
                return new UserResource($this->driver);
            }),
            
            'branch' => $this->whenLoaded('branch', function() {
                return new BranchResource($this->branch);
            }),
            
            'delivery' => $this->whenLoaded('delivery', function() {
                return new DeliveryResource($this->delivery);
            }),
            
            'payments' => $this->whenLoaded('payments', function() {
                return PaymentResource::collection($this->payments);
            }),
            
            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_iso' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'updated_at_iso' => $this->updated_at?->toIso8601String(),

            'dias_transcurridos' => $this->created_at
                ? $this->created_at->diffInDays(now())
                : null,
        ];
    }
}