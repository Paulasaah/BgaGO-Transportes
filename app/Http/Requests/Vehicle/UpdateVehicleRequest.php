<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\VehicleType;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado
     */
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    /**
     * Reglas de validación
     */
    public function rules(): array
    {
        $vehicleId = $this->route('vehicle')->id;

        return [
            'placa' => [
                'sometimes',
                'required',
                'string',
                'max:10',
                Rule::unique('vehicles', 'placa')->ignore($vehicleId),
                'regex:/^[A-Z]{3}-[0-9]{3}$/',
            ],
            'marca' => 'sometimes|required|string|max:50',
            'modelo' => 'sometimes|required|string|max:50',
            'year' => 'sometimes|required|integer|min:2015|max:' . (date('Y') + 1),
            'tipo' => ['sometimes', 'required', Rule::enum(VehicleType::class)],
            'color' => 'sometimes|required|string|max:30',
            'sede_id' => 'sometimes|required|exists:branches,id',
            'conductor_id' => 'nullable|exists:users,id',
            'precio_hora' => 'sometimes|required|numeric|min:1000|max:50000',
            'precio_dia' => 'sometimes|required|numeric|min:10000|max:200000',
            'estado' => 'sometimes|required|in:disponible,ocupado,mantenimiento,inactivo',
            'visible_catalogo' => 'sometimes|boolean',
            'descripcion' => 'nullable|string|max:1000',
            'imagen_principal' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    /**
     * Mensajes personalizados
     */
    public function messages(): array
    {
        return [
            'placa.regex' => 'La placa debe tener formato ABC-123',
            'placa.unique' => 'Ya existe otro vehículo con esta placa',
            'year.max' => 'El año no puede ser mayor a ' . (date('Y') + 1),
            'precio_hora.min' => 'El precio por hora mínimo es $1,000',
            'precio_dia.min' => 'El precio por día mínimo es $10,000',
            'imagen_principal.max' => 'La imagen no puede pesar más de 2MB',
            'conductor_id.exists' => 'El conductor seleccionado no existe',
            'sede_id.exists' => 'La sede seleccionada no existe',
        ];
    }

    /**
     * Atributos amigables
     */
    public function attributes(): array
    {
        return [
            'placa' => 'placa',
            'marca' => 'marca',
            'modelo' => 'modelo',
            'year' => 'año',
            'tipo' => 'tipo de vehículo',
            'color' => 'color',
            'sede_id' => 'sede',
            'conductor_id' => 'conductor',
            'precio_hora' => 'precio por hora',
            'precio_dia' => 'precio por día',
            'estado' => 'estado',
            'visible_catalogo' => 'visible en catálogo',
            'descripcion' => 'descripción',
            'imagen_principal' => 'imagen principal',
        ];
    }

    /**
     * Preparación previa a validación
     */
    protected function prepareForValidation(): void
    {
        // Normalizar placa a mayúsculas
        if ($this->has('placa')) {
            $this->merge([
                'placa' => strtoupper($this->placa),
            ]);
        }

        // Convertir visible_catalogo a boolean
        if ($this->has('visible_catalogo')) {
            $this->merge([
                'visible_catalogo' => $this->boolean('visible_catalogo'),
            ]);
        }
    }

    /**
     * Validaciones adicionales
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $vehicle = $this->route('vehicle');

            // Si se intenta cambiar de conductor, verificar que el conductor esté activo
            if ($this->filled('conductor_id')) {
                $conductor = \App\Models\User::find($this->conductor_id);
                
                if ($conductor && !$conductor->isAvailableDriver()) {
                    $validator->errors()->add(
                        'conductor_id',
                        'El conductor seleccionado no está activo o disponible'
                    );
                }
            }

            // Si el vehículo tiene reservas activas, no permitir ciertos cambios
            if ($vehicle && $vehicle->hasActiveReservations()) {
                if ($this->has('estado') && $this->estado === 'inactivo') {
                    $validator->errors()->add(
                        'estado',
                        'No puedes desactivar un vehículo con reservas activas'
                    );
                }

                if ($this->has('sede_id') && $this->sede_id !== $vehicle->sede_id) {
                    $validator->errors()->add(
                        'sede_id',
                        'No puedes cambiar la sede de un vehículo con reservas activas'
                    );
                }
            }

            // Validar coherencia de precios
            if ($this->filled('precio_hora') && $this->filled('precio_dia')) {
                if ($this->precio_dia < ($this->precio_hora * 8)) {
                    $validator->errors()->add(
                        'precio_dia',
                        'El precio por día debe ser al menos 8 veces el precio por hora'
                    );
                }
            }
        });
    }
}