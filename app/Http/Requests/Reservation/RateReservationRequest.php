<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;

class RateReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $reservation = $this->route('reservation');
        
        // Solo el cliente que hizo la reserva puede calificar
        return $reservation && $reservation->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'calificacion_conductor' => [
                'required',
                'integer',
                'between:1,5',
            ],
            'comentario_conductor' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'calificacion_conductor.required' => 'Debes seleccionar una calificación',
            'calificacion_conductor.between' => 'La calificación debe ser entre 1 y 5 estrellas',
            'comentario_conductor.max' => 'El comentario no puede exceder 500 caracteres',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'calificacion_conductor' => 'calificación',
            'comentario_conductor' => 'comentario',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $reservation = $this->route('reservation');
            
            if (!$reservation) {
                return;
            }

            // Verificar que la reserva esté completada
            if (!$reservation->isCompletada()) {
                $validator->errors()->add(
                    'estado',
                    'Solo puedes calificar reservas completadas'
                );
            }

            // Verificar que no haya sido calificada previamente
            if ($reservation->calificacion_conductor) {
                $validator->errors()->add(
                    'calificacion',
                    'Esta reserva ya fue calificada'
                );
            }

            // Verificar que tenga conductor asignado
            if (!$reservation->conductor_id) {
                $validator->errors()->add(
                    'conductor',
                    'Esta reserva no tiene conductor asignado'
                );
            }
        });
    }
}