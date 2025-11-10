<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleDeliveryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'vehiculo_id' => [
                'required',
                'integer',
                'exists:vehicles,id',
            ],
            'sede_id' => [
                'required',
                'integer',
                'exists:branches,id',
            ],
            
            // Origen (sede)
            'origen_direccion' => [
                'required',
                'string',
                'max:255',
            ],
            'origen_lat' => [
                'required',
                'numeric',
                'between:-90,90',
            ],
            'origen_lng' => [
                'required',
                'numeric',
                'between:-180,180',
            ],
            
            // Destino (cliente)
            'destino_direccion' => [
                'required',
                'string',
                'max:255',
            ],
            'destino_lat' => [
                'required',
                'numeric',
                'between:-90,90',
            ],
            'destino_lng' => [
                'required',
                'numeric',
                'between:-180,180',
            ],
            
            // Datos del destinatario
            'nombre_destinatario' => [
                'required',
                'string',
                'max:100',
            ],
            'telefono_destinatario' => [
                'required',
                'string',
                'regex:/^\+?57\s?\d{3}\s?\d{3}\s?\d{4}$/',
            ],
            
            // Programación
            'fecha_entrega' => [
                'nullable',
                'date',
                'after_or_equal:now',
            ],
            
            'instrucciones_especiales' => [
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
            'vehiculo_id.required' => 'Debes seleccionar un vehículo',
            'vehiculo_id.exists' => 'El vehículo seleccionado no existe',
            'sede_id.required' => 'Debes seleccionar una sede',
            'sede_id.exists' => 'La sede seleccionada no existe',
            
            'origen_direccion.required' => 'La dirección de origen es obligatoria',
            'destino_direccion.required' => 'La dirección de destino es obligatoria',
            
            'nombre_destinatario.required' => 'El nombre del destinatario es obligatorio',
            'telefono_destinatario.required' => 'El teléfono del destinatario es obligatorio',
            'telefono_destinatario.regex' => 'El teléfono debe ser válido (ej: +57 300 123 4567)',
            
            'fecha_entrega.after_or_equal' => 'La fecha de entrega debe ser futura',
            'instrucciones_especiales.max' => 'Las instrucciones no pueden exceder 500 caracteres',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validar que el vehículo esté disponible
            $vehicle = \App\Models\Vehicle::find($this->vehiculo_id);
            
            if ($vehicle && !$vehicle->isDisponible()) {
                $validator->errors()->add(
                    'vehiculo_id',
                    'El vehículo seleccionado no está disponible'
                );
            }

            // Validar distancia
            if ($this->origen_lat && $this->origen_lng && 
                $this->destino_lat && $this->destino_lng) {
                
                $distancia = $this->calculateDistance(
                    $this->origen_lat,
                    $this->origen_lng,
                    $this->destino_lat,
                    $this->destino_lng
                );

                if ($distancia < 0.5) {
                    $validator->errors()->add(
                        'destino_direccion',
                        'El destino debe estar al menos a 500 metros del origen'
                    );
                }

                if ($distancia > 50) {
                    $validator->errors()->add(
                        'destino_direccion',
                        'Por el momento solo cubrimos distancias de hasta 50 km'
                    );
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => auth()->id(),
        ]);

        if ($this->has('telefono_destinatario')) {
            $this->merge([
                'telefono_destinatario' => $this->normalizePhone($this->telefono_destinatario),
            ]);
        }
    }

    /**
     * Normalizar teléfono
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[\s\-]/', '', $phone);
        
        if (!str_starts_with($phone, '+57')) {
            $phone = '+57' . ltrim($phone, '57');
        }
        
        return $phone;
    }

    /**
     * Calcular distancia
     */
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371;

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}