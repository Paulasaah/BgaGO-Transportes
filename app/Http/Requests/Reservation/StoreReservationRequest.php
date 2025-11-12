<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\VehicleType;
use Carbon\Carbon;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // El usuario debe estar autenticado
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
            'fecha_inicio' => [
                'required',
                'date',
                'after:now',
            ],
            'fecha_fin' => [
                'required',
                'date',
                'after:fecha_inicio',
            ],
            'origen_direccion'   => [
                'required',
                'string',
                'max:255',
            ],
            'destino_direccion'  => [
                'nullable',
                'string',
                'max:255',
            ],
            'notas_cliente' => [
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
            // vehiculo_id
            'vehiculo_id.required' => 'Debes seleccionar un vehículo',
            'vehiculo_id.exists' => 'El vehículo seleccionado no existe',
            
            // sede_id
            'sede_id.required' => 'Debes seleccionar una sede',
            'sede_id.exists' => 'La sede seleccionada no existe',
            
            // fecha_inicio
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_inicio.date' => 'La fecha de inicio no es válida',
            'fecha_inicio.after' => 'La fecha de inicio debe ser futura',
            
            // fecha_fin
            'fecha_fin.required' => 'La fecha de fin es obligatoria',
            'fecha_fin.date' => 'La fecha de fin no es válida',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
            
            // notas_cliente
            'notas_cliente.max' => 'Las notas no pueden exceder 500 caracteres',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'vehiculo_id' => 'vehículo',
            'sede_id' => 'sede',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'notas_cliente' => 'notas',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validación personalizada: duración mínima de 1 hora
            if ($this->fecha_inicio && $this->fecha_fin) {
                $inicio = Carbon::parse($this->fecha_inicio);
                $fin = Carbon::parse($this->fecha_fin);
                
                if ($inicio->diffInMinutes($fin) < 60) {
                    $validator->errors()->add(
                        'fecha_fin',
                        'La reserva debe ser de al menos 1 hora'
                    );
                }

                // Validación: no más de 30 días
                if ($inicio->diffInDays($fin) > 30) {
                    $validator->errors()->add(
                        'fecha_fin',
                        'La reserva no puede exceder 30 días'
                    );
                }
            }

            // Validar que el usuario no tenga demasiadas reservas activas
            $activeReservations = auth()->user()->reservations()
                ->whereIn('estado', ['pendiente', 'confirmada', 'activa'])
                ->count();

            if ($activeReservations >= 3) {
                $validator->errors()->add(
                    'user',
                    'No puedes tener más de 3 reservas activas simultáneamente'
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

        // Normalizar fechas si vienen en formato diferente
        if ($this->has('fecha_inicio')) {
            $this->merge([
                'fecha_inicio' => Carbon::parse($this->fecha_inicio)->format('Y-m-d H:i:s'),
            ]);
        }

        if ($this->has('fecha_fin')) {
            $this->merge([
                'fecha_fin' => Carbon::parse($this->fecha_fin)->format('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Get validated data with additional computed values
     */
    public function validatedWithDefaults(): array
    {
        $validated = $this->validated();
        
        // Agregar user_id
        $validated['user_id'] = auth()->id();
        
        return $validated;
    }
}