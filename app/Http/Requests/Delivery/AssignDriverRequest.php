<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class AssignDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo admins pueden asignar conductores
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'conductor_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $conductor = \App\Models\User::find($this->conductor_id);
            
            // Verificar que sea conductor
            if ($conductor && !$conductor->isDriver()) {
                $validator->errors()->add(
                    'conductor_id',
                    'El usuario seleccionado no es conductor'
                );
            }

            // Verificar que esté disponible
            if ($conductor && !$conductor->isAvailableDriver()) {
                $validator->errors()->add(
                    'conductor_id',
                    'El conductor no está disponible'
                );
            }
        });
    }
}