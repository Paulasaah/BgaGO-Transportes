<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Reservation;

class CancelReservationRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar la solicitud.
     */
    public function authorize(): bool
    {
        $reservation = $this->route('reservation');

        // ✅ Solo el dueño o un admin pueden cancelar
        return $reservation && (
            $reservation->user_id === auth()->id() ||
            (auth()->check() && auth()->user()->isAdmin())
        );
    }

    /**
     * Reglas de validación.
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
     * Mensajes personalizados.
     */
    public function messages(): array
    {
        return [
            'motivo_cancelacion.required' => 'Debes indicar el motivo de la cancelación.',
            'motivo_cancelacion.min' => 'El motivo debe tener al menos 10 caracteres.',
            'motivo_cancelacion.max' => 'El motivo no puede exceder 500 caracteres.',
        ];
    }

    /**
     * Nombres amigables de los atributos.
     */
    public function attributes(): array
    {
        return [
            'motivo_cancelacion' => 'motivo de cancelación',
        ];
    }

    /**
     * Validaciones adicionales personalizadas.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $reservation = $this->route('reservation');

            if (!$reservation instanceof Reservation) {
                return;
            }

            // ⚠️ No permitir cancelar si el estado no lo permite
            if (method_exists($reservation, 'canBeCancelled') && !$reservation->canBeCancelled()) {
                $validator->errors()->add(
                    'estado',
                    'Esta reserva no puede ser cancelada en su estado actual (' . $reservation->estado->label() . ').'
                );
            }

            // ⚠️ Penalización si ya inició
            if ($reservation->fecha_inicio && $reservation->fecha_inicio->isPast()) {
                $validator->errors()->add(
                    'penalizacion',
                    'La reserva ya ha iniciado. Se aplicará una penalización del 50% según las políticas del servicio.'
                );
            }
        });
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
