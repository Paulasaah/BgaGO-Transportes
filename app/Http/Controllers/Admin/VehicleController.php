<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Branch;
use App\Enums\VehicleType;
use App\Enums\VehicleStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    /**
     * Mostrar listado de vehículos.
     */
    public function index()
    {
        return view('admin.vehicles.index');
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $conductores = User::role('conductor')
            ->with('driverProfile')
            ->whereHas('driverProfile', fn($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get();

        $sedes = Branch::orderBy('nombre')->get();

        $tipos = VehicleType::cases();

        return view('admin.vehicles.create', compact('conductores', 'sedes', 'tipos'));
    }

    /**
     * Almacenar nuevo vehículo.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:20|unique:vehicles,placa',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'tipo' => 'required|string|in:' . implode(',', array_column(VehicleType::cases(), 'value')),
            'color' => 'nullable|string|max:50',
            'capacidad' => 'required|integer|min:1|max:100',
            'conductor_id' => 'nullable|exists:users,id',
            'sede_id' => 'nullable|exists:branches,id',
            'estado' => 'required|string|in:' . implode(',', array_column(VehicleStatus::cases(), 'value')),

            // Información adicional
            'numero_motor' => 'nullable|string|max:100',
            'numero_chasis' => 'nullable|string|max:100',
            'kilometraje' => 'nullable|integer|min:0',
            'fecha_compra' => 'nullable|date',
            'precio_compra' => 'nullable|numeric|min:0',

            // Seguros y documentos
            'poliza_seguro' => 'nullable|string|max:100',
            'aseguradora' => 'nullable|string|max:100',
            'fecha_vencimiento_seguro' => 'nullable|date',
            'fecha_vencimiento_soat' => 'nullable|date',
            'fecha_vencimiento_tecnicomecanica' => 'nullable|date',

            // Características
            'tiene_aire_acondicionado' => 'boolean',
            'tiene_gps' => 'boolean',

            // Observaciones
            'observaciones' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Convertir booleanos
            $validated['tiene_aire_acondicionado'] = $request->has('tiene_aire_acondicionado');
            $validated['tiene_gps'] = $request->has('tiene_gps');

            $vehicle = Vehicle::create($validated);

            DB::commit();

            return redirect()
                ->route('admin.vehicles.index')
                ->with('success', "Vehículo {$vehicle->placa} creado exitosamente");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear vehículo: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el vehículo: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalles del vehículo.
     */
    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['driver', 'branch', 'reservations' => function ($q) {
            $q->latest()->limit(10);
        }]);

        return view('admin.vehicles.show', compact('vehicle'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Vehicle $vehicle)
    {
        $conductores = User::role('conductor')
            ->with('driverProfile')
            ->whereHas('driverProfile', fn($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get();

        $sedes = Branch::orderBy('nombre')->get();

        $tipos = VehicleType::cases();

        $vehicle->load(['driver', 'branch']);

        return view('admin.vehicles.edit', compact('vehicle', 'conductores', 'sedes', 'tipos'));
    }

    /**
     * Actualizar vehículo existente.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:20|unique:vehicles,placa,' . $vehicle->id,
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'tipo' => 'required|string|in:' . implode(',', array_column(VehicleType::cases(), 'value')),
            'color' => 'nullable|string|max:50',
            'capacidad' => 'required|integer|min:1|max:100',
            'conductor_id' => 'nullable|exists:users,id',
            'sede_id' => 'nullable|exists:branches,id',
            'estado' => 'required|string|in:' . implode(',', array_column(VehicleStatus::cases(), 'value')),

            // Información adicional
            'numero_motor' => 'nullable|string|max:100',
            'numero_chasis' => 'nullable|string|max:100',
            'kilometraje' => 'nullable|integer|min:0',
            'fecha_compra' => 'nullable|date',
            'precio_compra' => 'nullable|numeric|min:0',

            // Seguros y documentos
            'poliza_seguro' => 'nullable|string|max:100',
            'aseguradora' => 'nullable|string|max:100',
            'fecha_vencimiento_seguro' => 'nullable|date',
            'fecha_vencimiento_soat' => 'nullable|date',
            'fecha_vencimiento_tecnicomecanica' => 'nullable|date',

            // Características
            'tiene_aire_acondicionado' => 'boolean',
            'tiene_gps' => 'boolean',

            // Observaciones
            'observaciones' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $validated['tiene_aire_acondicionado'] = $request->has('tiene_aire_acondicionado');
            $validated['tiene_gps'] = $request->has('tiene_gps');

            $vehicle->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.vehicles.index')
                ->with('success', "Vehículo {$vehicle->placa} actualizado exitosamente");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al actualizar vehículo: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el vehículo: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar vehículo.
     */
    public function destroy(Vehicle $vehicle)
    {
        try {
            if ($vehicle->reservations()->whereIn('estado', ['pendiente', 'confirmada', 'activa'])->exists()) {
                return redirect()
                    ->back()
                    ->with('error', 'No se puede eliminar un vehículo con reservas activas');
            }

            $placa = $vehicle->placa;
            $vehicle->delete();

            return redirect()
                ->route('admin.vehicles.index')
                ->with('success', "Vehículo {$placa} eliminado exitosamente");
        } catch (\Exception $e) {
            \Log::error('Error al eliminar vehículo: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Error al eliminar el vehículo: ' . $e->getMessage());
        }
    }
}
