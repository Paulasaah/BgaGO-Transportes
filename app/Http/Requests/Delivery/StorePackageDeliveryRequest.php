<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageDeliveryRequest extends FormRequest
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
            'sede_id' => [
                'required',
                'integer',
                'exists:branches,id',
            ],
            
            // Origen
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
            
            // Destino
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
            
            // Datos del remitente
            'nombre_remitente' => [
                'required',
                'string',
                'max:100',
            ],
            'telefono_remitente' => [
                'required',
                'string',
                'regex:/^\+?57\s?\d{3}\s?\d{3}\s?\d{4}$/',
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
            
            // Detalles del paquete
            'descripcion_contenido' => [
                'nullable',
                'string',
                'max:500',
            ],
            'peso_kg' => [
                'nullable',
                'numeric',
                'min:0.1',
                'max:50',
            ],
            'requiere_firma' => [
                'boolean',
            ],
            'es_fragil' => [
                'boolean',
            ],
            'instrucciones_especiales' => [
                'nullable',
                'string',
                'max:500',
            ],
            
            // Programación
            'fecha_recogida' => [
                'nullable',
                'date',
                'after_or_equal:now',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Sede
            'sede_id.required' => 'Debes seleccionar una sede',
            'sede_id.exists' => 'La sede seleccionada no existe',
            
            // Origen
            'origen_direccion.required' => 'La dirección de origen es obligatoria',
            'origen_lat.required' => 'Debes seleccionar el origen en el mapa',
            'origen_lng.required' => 'Debes seleccionar el origen en el mapa',
            'origen_lat.between' => 'Coordenadas de origen inválidas',
            'origen_lng.between' => 'Coordenadas de origen inválidas',
            
            // Destino
            'destino_direccion.required' => 'La dirección de destino es obligatoria',
            'destino_lat.required' => 'Debes seleccionar el destino en el mapa',
            'destino_lng.required' => 'Debes seleccionar el destino en el mapa',
            'destino_lat.between' => 'Coordenadas de destino inválidas',
            'destino_lng.between' => 'Coordenadas de destino inválidas',
            
            // Remitente
            'nombre_remitente.required' => 'El nombre del remitente es obligatorio',
            'telefono_remitente.required' => 'El teléfono del remitente es obligatorio',
            'telefono_remitente.regex' => 'El teléfono debe ser válido (ej: +57 300 123 4567)',
            
            // Destinatario
            'nombre_destinatario.required' => 'El nombre del destinatario es obligatorio',
            'telefono_destinatario.required' => 'El teléfono del destinatario es obligatorio',
            'telefono_destinatario.regex' => 'El teléfono debe ser válido (ej: +57 300 123 4567)',
            
            // Paquete
            'peso_kg.min' => 'El peso mínimo es 0.1 kg',
            'peso_kg.max' => 'El peso máximo permitido es 50 kg',
            'descripcion_contenido.max' => 'La descripción no puede exceder 500 caracteres',
            'instrucciones_especiales.max' => 'Las instrucciones no pueden exceder 500 caracteres',
            
            // Fecha
            'fecha_recogida.after_or_equal' => 'La fecha de recogida debe ser futura',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'sede_id' => 'sede',
            'origen_direccion' => 'dirección de origen',
            'destino_direccion' => 'dirección de destino',
            'nombre_remitente' => 'nombre del remitente',
            'telefono_remitente' => 'teléfono del remitente',
            'nombre_destinatario' => 'nombre del destinatario',
            'telefono_destinatario' => 'teléfono del destinatario',
            'peso_kg' => 'peso',
            'fecha_recogida' => 'fecha de recogida',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validar que origen y destino no sean el mismo punto
            if ($this->origen_lat && $this->origen_lng && 
                $this->destino_lat && $this->destino_lng) {
                
                $distancia = $this->calculateDistance(
                    $this->origen_lat,
                    $this->origen_lng,
                    $this->destino_lat,
                    $this->destino_lng
                );

                // Distancia mínima de 500 metros
                if ($distancia < 0.5) {
                    $validator->errors()->add(
                        'destino_direccion',
                        'El destino debe estar al menos a 500 metros del origen'
                    );
                }

                // Distancia máxima de 50 km
                if ($distancia > 50) {
                    $validator->errors()->add(
                        'destino_direccion',
                        'Por el momento solo cubrimos distancias de hasta 50 km'
                    );
                }
            }

            // Validar peso si se marca como frágil
            if ($this->es_fragil && $this->peso_kg > 10) {
                $validator->errors()->add(
                    'peso_kg',
                    'Los paquetes frágiles no pueden pesar más de 10 kg'
                );
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Agregar user_id automáticamente
        $this->merge([
            'user_id' => auth()->id(),
        ]);

        // Normalizar teléfonos
        if ($this->has('telefono_remitente')) {
            $this->merge([
                'telefono_remitente' => $this->normalizePhone($this->telefono_remitente),
            ]);
        }

        if ($this->has('telefono_destinatario')) {
            $this->merge([
                'telefono_destinatario' => $this->normalizePhone($this->telefono_destinatario),
            ]);
        }

        // Defaults
        $this->merge([
            'requiere_firma' => $this->boolean('requiere_firma'),
            'es_fragil' => $this->boolean('es_fragil'),
        ]);
    }

    /**
     * Normalizar formato de teléfono
     */
    protected function normalizePhone(string $phone): string
    {
        // Eliminar espacios y guiones
        $phone = preg_replace('/[\s\-]/', '', $phone);
        
        // Agregar +57 si no lo tiene
        if (!str_starts_with($phone, '+57')) {
            $phone = '+57' . ltrim($phone, '57');
        }
        
        return $phone;
    }

    /**
     * Calcular distancia (Haversine)
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