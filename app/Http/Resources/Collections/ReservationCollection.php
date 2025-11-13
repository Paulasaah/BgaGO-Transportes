<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ReservationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
                'stats' => [
                    'pendientes' => $this->collection->where('estado.value', 'pendiente')->count(),
                    'confirmadas' => $this->collection->where('estado.value', 'confirmada')->count(),
                    'activas' => $this->collection->where('estado.value', 'activa')->count(),
                    'completadas' => $this->collection->where('estado.value', 'completada')->count(),
                    'canceladas' => $this->collection->where('estado.value', 'cancelada')->count(),
                ],
                'totals' => [
                    'monto_total' => $this->collection->sum('monto_final'),
                    'monto_total_formatted' => '$' . number_format(
                        $this->collection->sum('monto_final'),
                        0, ',', '.'
                    ),
                ],
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'Reservas obtenidas exitosamente',
        ];
    }
}