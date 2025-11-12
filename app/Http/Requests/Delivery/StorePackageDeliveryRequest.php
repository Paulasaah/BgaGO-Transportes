<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageDeliveryRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        return [
            'sede_id' => [
                'required',
                'integer',
                'exists:branches,id',
            ],

            'vehiculo_id' => [
                'nullable',
                'integer',
                'exists:vehicles,id',
            ],

            // Origen
            'direccion_origen' => [
                'required',
                'string',
                'max:255',
            ],
            'lat_origen' => [
                'required',
                'numeric',
                'between:-90,90',
            ],
            'lon_origen' => [
                'required',
                'numeric',
                'between:-180,180',
            ],

            // Destino
            'direccion_destino' => [
                'required',
                'string',
                'max:255',
            ],
            'lat_destino' => [
                'required',
                'numeric',
                'between:-90,90',
            ],
            'lon_destino' => [
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
            'peso_estimado' => [
                'nullable',
                'numeric',
                'min:0.1',
                'max:50',
            ],
            'requiere_firma' => [
                'nullable',
                'boolean',
            ],
            'es_fragil' => [
                'nullable',
                'boolean',
            ],
            'instrucciones_especiales' => [
                'nullable',
                'string',
                'max:500',
            ],
            'costo' => [
                'nullable',
                'numeric',
                'min:0',
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
     * Mensajes personalizados.
     */
    public function messages(): array
    {
        return [
            'sede_id.required' => 'Debes seleccionar una sede',
            'sede_id.exists' => 'La sede seleccionada no existe',

            // Origen
            'direccion_origen.required' => 'La dirección de origen es obligatoria',
            'lat_origen.required' => 'Debes seleccionar el origen en el mapa',
            'lon_origen.required' => 'Debes seleccionar el origen en el mapa',
            'lat_origen.between' => 'Coordenadas de origen inválidas',
            'lon_origen.between' => 'Coordenadas de origen inválidas',

            // Destino
            'direccion_destino.required' => 'La dirección de destino es obligatoria',
            'lat_destino.required' => 'Debes seleccionar el destino en el mapa',
            'lon_destino.required' => 'Debes seleccionar el destino en el mapa',
            'lat_destino.between' => 'Coordenadas de destino inválidas',
            'lon_destino.between' => 'Coordenadas de destino inválidas',

            // Remitente
            'nombre_remitente.required' => 'El nombre del remitente es obligatorio',
            'telefono_remitente.required' => 'El teléfono del remitente es obligatorio',
            'telefono_remitente.regex' => 'El teléfono debe ser válido (ej: +57 300 123 4567)',

            // Destinatario
            'nombre_destinatario.required' => 'El nombre del destinatario es obligatorio',
            'telefono_destinatario.required' => 'El teléfono del destinatario es obligatorio',
            'telefono_destinatario.regex' => 'El teléfono debe ser válido (ej: +57 300 123 4567)',

            // Paquete
            'peso_estimado.min' => 'El peso mínimo es 0.1 kg',
            'peso_estimado.max' => 'El peso máximo permitido es 50 kg',
            'descripcion_contenido.max' => 'La descripción no puede exceder 500 caracteres',
            'instrucciones_especiales.max' => 'Las instrucciones no pueden exceder 500 caracteres',

            'fecha_recogida.after_or_equal' => 'La fecha de recogida debe ser futura',
        ];
    }

    /**
     * Nombres amigables.
     */
    public function attributes(): array
    {
        return [
            'sede_id' => 'sede',
            'direccion_origen' => 'dirección de origen',
            'direccion_destino' => 'dirección de destino',
            'nombre_remitente' => 'nombre del remitente',
            'telefono_remitente' => 'teléfono del remitente',
            'nombre_destinatario' => 'nombre del destinatario',
            'telefono_destinatario' => 'teléfono del destinatario',
            'peso_estimado' => 'peso estimado',
            'fecha_recogida' => 'fecha de recogida',
        ];
    }

    /**
     * Validaciones adicionales.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->lat_origen && $this->lon_origen && 
                $this->lat_destino && $this->lon_destino) {
                
                $distancia = $this->calculateDistance(
                    $this->lat_origen,
                    $this->lon_origen,
                    $this->lat_destino,
                    $this->lon_destino
                );

                if ($distancia < 0.5) {
                    $validator->errors()->add(
                        'direccion_destino',
                        'El destino debe estar al menos a 500 metros del origen.'
                    );
                }

                if ($distancia > 50) {
                    $validator->errors()->add(
                        'direccion_destino',
                        'Por el momento solo cubrimos distancias de hasta 50 km.'
                    );
                }
            }

            if ($this->es_fragil && $this->peso_estimado > 10) {
                $validator->errors()->add(
                    'peso_estimado',
                    'Los paquetes frágiles no pueden pesar más de 10 kg.'
                );
            }
        });
    }

    /**
     * Preparación previa.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => auth()->id(),
            'requiere_firma' => $this->boolean('requiere_firma'),
            'es_fragil' => $this->boolean('es_fragil'),
        ]);

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
    }

    /**
     * Normaliza el formato del teléfono.
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
     * Calcula la distancia (Haversine).
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

        $a = sin($latDelta / 2) ** 2 +
             cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 2);
    }
}
