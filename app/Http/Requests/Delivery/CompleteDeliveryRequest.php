<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class CompleteDeliveryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $delivery = $this->route('delivery');
        
        if (!$delivery) {
            return false;
        }

        // Solo el conductor asignado o un admin
        return auth()->id() === $delivery->reservation->conductor_id ||
               auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'firma' => [
                'nullable',
                'string',
                'max:10000', // Base64 image
            ],
            'foto' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:5120', // 5MB
            ],
            'notas' => [
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
            'foto.image' => 'El archivo debe ser una imagen',
            'foto.mimes' => 'La foto debe ser JPG o PNG',
            'foto.max' => 'La foto no puede pesar más de 5MB',
            'notas.max' => 'Las notas no pueden exceder 500 caracteres',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $delivery = $this->route('delivery');
            
            if (!$delivery) {
                return;
            }

            // Verificar que el delivery está activo
            if (!$delivery->reservation->isActiva()) {
                $validator->errors()->add(
                    'estado',
                    'Este domicilio no está activo'
                );
            }

            // Verificar que ya fue recogido
            if (!$delivery->fecha_recogida) {
                $validator->errors()->add(
                    'estado',
                    'Primero debes marcar el domicilio como recogido'
                );
            }

            // Verificar firma si es requerida
            if ($delivery->requiere_firma && !$this->has('firma')) {
                $validator->errors()->add(
                    'firma',
                    'Este domicilio requiere firma del destinatario'
                );
            }
        });
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Procesar la foto si existe
        if ($this->hasFile('foto')) {
            $path = $this->file('foto')->store('deliveries/photos', 'public');
            $this->merge(['foto_entrega' => $path]);
        }

        // Guardar firma si existe
        if ($this->has('firma')) {
            $this->merge(['firma_destinatario' => $this->firma]);
        }

        // Guardar notas
        if ($this->has('notas')) {
            $this->merge(['notas_entrega' => $this->notas]);
        }
    }
}