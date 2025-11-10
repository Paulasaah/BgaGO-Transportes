<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\VehicleType;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'placa' => [
                'required',
                'string',
                'max:10',
                'unique:vehicles,placa',
                'regex:/^[A-Z]{3}-[0-9]{3}$/',
            ],
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'year' => 'required|integer|min:2015|max:' . (date('Y') + 1),
            'tipo' => ['required', Rule::enum(VehicleType::class)],
            'color' => 'required|string|max:30',
            'sede_id' => 'required|exists:branches,id',
            'conductor_id' => 'nullable|exists:users,id',
            'precio_hora' => 'required|numeric|min:1000|max:50000',
            'precio_dia' => 'required|numeric|min:10000|max:200000',
            'visible_catalogo' => 'boolean',
            'descripcion' => 'nullable|string|max:1000',
            'imagen_principal' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'placa.regex' => 'La placa debe tener formato ABC-123',
            'placa.unique' => 'Ya existe un vehículo con esta placa',
            'year.max' => 'El año no puede ser mayor a ' . (date('Y') + 1),
            'precio_hora.min' => 'El precio por hora mínimo es $1,000',
            'imagen_principal.max' => 'La imagen no puede pesar más de 2MB',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Normalizar placa a mayúsculas
        if ($this->has('placa')) {
            $this->merge([
                'placa' => strtoupper($this->placa),
            ]);
        }

        // Default visible_catalogo
        $this->merge([
            'visible_catalogo' => $this->boolean('visible_catalogo', true),
        ]);
    }
}