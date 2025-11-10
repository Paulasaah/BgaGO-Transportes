<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Reservation;

class CancelReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $reservation = $this->route('reservation');
        
        // El usuario debe ser el dueño de la reserva o un admin
        return $reservation && (
            $reservation->user_id === auth()->id() ||
            auth()->user()->isAdmin()
        );
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'motivo_cancelacion' => [
                'required',
                'string',
                'min:10',
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
            'motivo_cancelacion.required' => 'Debes indicar el motivo de la cancelación',
            'motivo_cancelacion.min' => 'El motivo debe tener al menos 10 caracteres',
            'motivo_cancelacion.max' => 'El motivo no puede exceder 500 caracteres',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'motivo_cancelacion' => 'motivo de cancelación',
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

            // Verificar que la reserva puede ser cancelada
            if (!$reservation->canBeCancelled()) {
                $validator->errors()->add(
                    'estado',
                    'Esta reserva no puede ser cancelada en su estado actual (' . $reservation->estado->label() . ')'
                );
            }

            // Si ya inició, cobrar penalización
            if ($reservation->fecha_inicio->isPast()) {
                $validator->errors()->add(
                    'fecha',
                    'La reserva ya ha iniciado. Se aplicará una penalización del 50%'
                );
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cancelado_por' => auth()->id(),
        ]);
    }
}