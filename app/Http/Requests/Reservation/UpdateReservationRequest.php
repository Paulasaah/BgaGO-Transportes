<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $reservation = $this->route('reservation');
        return $reservation && $reservation->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'fecha_inicio' => 'sometimes|date|after:now',
            'fecha_fin' => 'sometimes|date|after:fecha_inicio',
            'notas_cliente' => 'nullable|string|max:500',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $reservation = $this->route('reservation');
            
            // Solo se puede modificar si está pendiente
            if ($reservation && !$reservation->isPendiente()) {
                $validator->errors()->add(
                    'estado',
                    'Solo puedes modificar reservas pendientes'
                );
            }
        });
    }
}