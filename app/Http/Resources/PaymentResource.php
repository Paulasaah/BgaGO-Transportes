<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'codigo_transaccion' => $this->codigo_transaccion,
            
            // Método de pago
            'metodo_pago' => $this->metodo_pago,
            
            // Estado con info del enum
            'estado' => [
                'value' => $this->estado->value,
                'label' => $this->estado->label(),
                'icon' => $this->estado->icon(),
                'color' => $this->estado->color(),
                'is_successful' => $this->estado->isSuccessful(),
                'is_final' => $this->estado->isFinal(),
            ],
            
            // Monto
            'monto' => (float) $this->monto,
            'monto_formatted' => '$' . number_format($this->monto, 0, ',', '.'),
            
            // Información de la transacción (solo para usuarios autorizados)
            'referencia_externa' => $this->when(
                $request->user()?->id === $this->user_id || $request->user()?->hasRole('admin'),
                $this->referencia_externa
            ),
            
            'datos_transaccion' => $this->when(
                $request->user()?->hasRole('admin'),
                $this->datos_transaccion
            ),
            
            // Motivo de rechazo (si aplica)
            'motivo_rechazo' => $this->when(
                $this->isRechazado() || $this->isReembolsado(),
                $this->motivo_rechazo
            ),
            
            // Fechas
            'fecha_aprobacion' => $this->fecha_aprobacion?->format('Y-m-d H:i:s'),
            
            // Flags
            'can_be_refunded' => $this->canBeRefunded(),
            
            // Relaciones
            'user' => $this->whenLoaded('user', function() {
                return new UserResource($this->user);
            }),
            
            'reservation' => $this->whenLoaded('reservation', function() {
                return [
                    'id' => $this->reservation->id,
                    'codigo' => $this->reservation->codigo,
                    'tipo' => $this->reservation->tipo->value,
                ];
            }),
            
            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}