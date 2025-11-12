<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Reservation;
use Illuminate\Validation\Validator; 


class CancelReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // La autorización se maneja en la Policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'motivo_cancelacion' => 'nullable|string|max:500',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $reservation = $this->route('reservation');
            $user = $this->user();

            // 🛡️ ADMINS Y SUPER_ADMINS pueden cancelar cualquier reserva sin restricciones
            if ($user && ($user->hasRole('admin') || $user->hasRole('super_admin'))) {
                return; // Saltar todas las validaciones
            }

            // 👤 USUARIOS NORMALES: Validar estado
            if (!$reservation->canBeCancelled()) {
                $validator->errors()->add(
                    'estado',
                    "Esta reserva no puede ser cancelada en su estado actual ({$reservation->estado->label()})."
                );
            }

            // ⏰ Validar si ya inició (penalización)
            if ($reservation->isActiva() || $reservation->isCompletada()) {
                $validator->errors()->add(
                    'penalizacion',
                    'La reserva ya ha iniciado. Se aplicará una penalización del 50% según las políticas del servicio.'
                );
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'motivo_cancelacion.string' => 'El motivo de cancelación debe ser texto.',
            'motivo_cancelacion.max' => 'El motivo de cancelación no puede exceder 500 caracteres.',
        ];
    }

    /**
     * Preparar los datos antes de la validación.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cancelado_por' => auth()->id(),
        ]);
    }
}
