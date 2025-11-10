<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class CompleteDeliveryRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta acción.
     */
    public function authorize(): bool
    {
        $delivery = $this->route('delivery');

        if (!$delivery || !auth()->check()) {
            return false;
        }

        // ✅ Solo el conductor asignado o un admin pueden completar el delivery
        return auth()->id() === optional($delivery->reservation)->conductor_id
            || auth()->user()->isAdmin();
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        return [
            'foto' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:5120', // Máx 5MB
            ],
            'notas' => [
                'nullable',
                'string',
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
            'foto.image' => 'El archivo debe ser una imagen válida.',
            'foto.mimes' => 'La foto debe estar en formato JPG o PNG.',
            'foto.max' => 'La foto no puede superar los 5MB.',
            'notas.max' => 'Las notas no pueden exceder los 500 caracteres.',
        ];
    }

    /**
     * Nombres legibles de los atributos.
     */
    public function attributes(): array
    {
        return [
            'foto' => 'foto de entrega',
            'notas' => 'notas de entrega',
        ];
    }

    /**
     * Validaciones adicionales personalizadas.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $delivery = $this->route('delivery');

            if (!$delivery || !isset($delivery->reservation)) {
                return;
            }

            // 🚫 Si la reserva ya no está activa
            if (method_exists($delivery->reservation, 'isActiva') && !$delivery->reservation->isActiva()) {
                $validator->errors()->add(
                    'estado',
                    'Este domicilio no está activo o ya fue completado.'
                );
            }

            // ⚠️ Verificar que haya sido recogido
            if (is_null($delivery->fecha_recogida)) {
                $validator->errors()->add(
                    'estado',
                    'Debes marcar el domicilio como recogido antes de completarlo.'
                );
            }
        });
    }

    /**
     * Post-procesamiento tras validación exitosa.
     */
    protected function passedValidation(): void
    {
        // 📸 Procesar foto si fue cargada
        if ($this->hasFile('foto')) {
            $path = $this->file('foto')->store('deliveries/photos', 'public');
            $this->merge(['foto_entrega' => $path]);
        }

        // 📝 Guardar notas si existen
        if ($this->filled('notas')) {
            $this->merge(['notas_entrega' => $this->notas]);
        }
    }
}
