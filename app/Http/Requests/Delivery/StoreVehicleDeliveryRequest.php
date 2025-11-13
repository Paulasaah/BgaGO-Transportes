<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Vehicle;

class StoreVehicleDeliveryRequest extends FormRequest
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

            // Destino (cliente)
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

            // Instrucciones y costo
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
        ];
    }

    /**
     * Mensajes personalizados.
     */
    public function messages(): array
    {
        return [
            'vehiculo_id.required' => 'Debes seleccionar un vehículo.',
            'vehiculo_id.exists' => 'El vehículo seleccionado no existe.',
            'sede_id.required' => 'Debes seleccionar una sede.',
            'sede_id.exists' => 'La sede seleccionada no existe.',

            'direccion_origen.required' => 'La dirección de origen es obligatoria.',
            'direccion_destino.required' => 'La dirección de destino es obligatoria.',

            'nombre_destinatario.required' => 'El nombre del destinatario es obligatorio.',
            'telefono_destinatario.required' => 'El teléfono del destinatario es obligatorio.',
            'telefono_destinatario.regex' => 'El teléfono debe ser válido (ej: +57 300 123 4567).',

            'fecha_entrega.after_or_equal' => 'La fecha de entrega debe ser futura.',
            'instrucciones_especiales.max' => 'Las instrucciones no pueden exceder 500 caracteres.',
        ];
    }

    /**
     * Nombres amigables.
     */
    public function attributes(): array
    {
        return [
            'vehiculo_id' => 'vehículo',
            'sede_id' => 'sede',
            'direccion_origen' => 'dirección de origen',
            'direccion_destino' => 'dirección de destino',
            'lat_origen' => 'latitud de origen',
            'lon_origen' => 'longitud de origen',
            'lat_destino' => 'latitud de destino',
            'lon_destino' => 'longitud de destino',
            'nombre_destinatario' => 'nombre del destinatario',
            'telefono_destinatario' => 'teléfono del destinatario',
            'fecha_entrega' => 'fecha de entrega',
            'costo' => 'costo del envío',
        ];
    }

    /**
     * Validaciones adicionales personalizadas.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validar disponibilidad del vehículo
            $vehicle = Vehicle::find($this->vehiculo_id);

            if ($vehicle && method_exists($vehicle, 'isDisponible') && !$vehicle->isDisponible()) {
                $validator->errors()->add(
                    'vehiculo_id',
                    'El vehículo seleccionado no está disponible actualmente.'
                );
            }

            // Validar distancia mínima y máxima
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
        });
    }

    /**
     * Prepara los datos antes de la validación.
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
     * Calcula la distancia entre dos coordenadas (Haversine).
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
