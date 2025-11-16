<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleMaintenance;
use App\Models\Vehicle;
use App\Enums\MaintenanceType;
use App\Enums\MaintenanceStatus;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of maintenances
     */
    public function index()
    {
        return view('admin.maintenances.index');
    }

    /**
     * Show the form for creating a new maintenance
     */
    public function create()
    {
        $vehicles = Vehicle::orderBy('placa')->get();
        
        return view('admin.maintenances.create', compact('vehicles'));
    }

    /**
     * Store a newly created maintenance
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehiculo_id' => 'required|exists:vehicles,id',
            'tipo' => 'required|in:' . implode(',', array_column(MaintenanceType::cases(), 'value')),
            'estado' => 'nullable|in:' . implode(',', array_column(MaintenanceStatus::cases(), 'value')),
            'kilometraje_actual' => 'nullable|integer|min:0',
            'kilometraje_proximo' => 'nullable|integer|min:0',
            'kilometraje_intervalo' => 'nullable|integer|min:0',
            'fecha_programada' => 'nullable|date',
            'fecha_realizada' => 'nullable|date',
            'costo' => 'nullable|numeric|min:0',
            'descripcion' => 'required|string|max:2000',
            'repuestos_usados' => 'nullable|string|max:1000',
            'taller' => 'nullable|string|max:255',
            'mecanico' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        // Asignar usuario que registra
        $validated['realizado_por'] = auth()->id();
        
        // Estado por defecto
        if (!isset($validated['estado'])) {
            $validated['estado'] = MaintenanceStatus::Programado;
        }

        // Calcular kilometraje próximo si no se proporciona
        if (!isset($validated['kilometraje_proximo']) && isset($validated['kilometraje_actual'])) {
            $tipo = MaintenanceType::from($validated['tipo']);
            $intervalo = $tipo->recommendedInterval();
            if ($intervalo) {
                $validated['kilometraje_proximo'] = $validated['kilometraje_actual'] + $intervalo;
                $validated['kilometraje_intervalo'] = $intervalo;
            }
        }

        $maintenance = VehicleMaintenance::create($validated);

        return redirect()
            ->route('admin.maintenances.show', $maintenance)
            ->with('notification', [
                'type' => 'success',
                'message' => 'Mantenimiento registrado exitosamente'
            ]);
    }

    /**
     * Display the specified maintenance
     */
    public function show(VehicleMaintenance $maintenance)
    {
        $maintenance->load(['vehicle', 'user']);
        return view('admin.maintenances.show', compact('maintenance'));
    }

    /**
     * Show the form for editing the specified maintenance
     */
    public function edit(VehicleMaintenance $maintenance)
    {
        if (!$maintenance->canEdit()) {
            return back()->with('notification', [
                'type' => 'error',
                'message' => 'No se puede editar un mantenimiento completado'
            ]);
        }

        $vehicles = Vehicle::orderBy('placa')->get();
        
        return view('admin.maintenances.edit', compact('maintenance', 'vehicles'));
    }

    /**
     * Update the specified maintenance
     */
    public function update(Request $request, VehicleMaintenance $maintenance)
    {
        if (!$maintenance->canEdit()) {
            return back()->with('notification', [
                'type' => 'error',
                'message' => 'No se puede editar un mantenimiento completado'
            ]);
        }

        $validated = $request->validate([
            'vehiculo_id' => 'required|exists:vehicles,id',
            'tipo' => 'required|in:' . implode(',', array_column(MaintenanceType::cases(), 'value')),
            'estado' => 'required|in:' . implode(',', array_column(MaintenanceStatus::cases(), 'value')),
            'kilometraje_actual' => 'nullable|integer|min:0',
            'kilometraje_proximo' => 'nullable|integer|min:0',
            'kilometraje_intervalo' => 'nullable|integer|min:0',
            'fecha_programada' => 'nullable|date',
            'fecha_realizada' => 'nullable|date',
            'costo' => 'nullable|numeric|min:0',
            'descripcion' => 'required|string|max:2000',
            'repuestos_usados' => 'nullable|string|max:1000',
            'taller' => 'nullable|string|max:255',
            'mecanico' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $maintenance->update($validated);

        return redirect()
            ->route('admin.maintenances.show', $maintenance)
            ->with('notification', [
                'type' => 'success',
                'message' => 'Mantenimiento actualizado exitosamente'
            ]);
    }

    /**
     * Remove the specified maintenance
     */
    public function destroy(VehicleMaintenance $maintenance)
    {
        if ($maintenance->estado === MaintenanceStatus::Completado) {
            return back()->with('notification', [
                'type' => 'error',
                'message' => 'No se puede eliminar un mantenimiento completado'
            ]);
        }

        $maintenance->delete();

        return redirect()
            ->route('admin.maintenances.index')
            ->with('notification', [
                'type' => 'success',
                'message' => 'Mantenimiento eliminado exitosamente'
            ]);
    }

    /**
     * Start maintenance
     */
    public function start(VehicleMaintenance $maintenance)
    {
        if ($maintenance->estado !== MaintenanceStatus::Programado) {
            return back()->with('notification', [
                'type' => 'error',
                'message' => 'Solo se pueden iniciar mantenimientos programados'
            ]);
        }

        $maintenance->start();

        return back()->with('notification', [
            'type' => 'success',
            'message' => 'Mantenimiento iniciado'
        ]);
    }

    /**
     * Complete maintenance
     */
    public function complete(VehicleMaintenance $maintenance)
    {
        if ($maintenance->estado === MaintenanceStatus::Completado) {
            return back()->with('notification', [
                'type' => 'error',
                'message' => 'El mantenimiento ya está completado'
            ]);
        }

        $maintenance->markAsCompleted();

        return back()->with('notification', [
            'type' => 'success',
            'message' => 'Mantenimiento completado exitosamente'
        ]);
    }

    /**
     * Cancel maintenance
     */
    public function cancel(VehicleMaintenance $maintenance)
    {
        if (!$maintenance->canCancel()) {
            return back()->with('notification', [
                'type' => 'error',
                'message' => 'No se puede cancelar este mantenimiento'
            ]);
        }

        $maintenance->cancel();

        return back()->with('notification', [
            'type' => 'success',
            'message' => 'Mantenimiento cancelado'
        ]);
    }
}
