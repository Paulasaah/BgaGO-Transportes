<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\InteractsWithApiController;
use App\Http\Controllers\Api\VehicleController as ApiController;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Branch;
use App\Enums\VehicleType;
use App\Enums\VehicleStatus;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    use InteractsWithApiController;

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

        $sedes = Branch::where('is_active', true)->orderBy('nombre')->get();
        $tipos = VehicleType::cases();

        return view('admin.vehicles.create', compact('conductores', 'sedes', 'tipos'));
    }

    /**
     * Almacenar nuevo vehículo (via API)
     */
    public function store(Request $request)
    {
        return $this->createViaApi(
            ApiController::class,
            $request,
            'admin.vehicles.index'
        );
    }

    /**
     * Mostrar detalles del vehículo.
     */
    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['driver', 'branch', 'reservations' => fn($q) => $q->latest()->limit(10)]);
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

        $sedes = Branch::where('is_active', true)->orderBy('nombre')->get();
        $tipos = VehicleType::cases();

        $vehicle->load(['driver', 'branch']);

        return view('admin.vehicles.edit', compact('vehicle', 'conductores', 'sedes', 'tipos'));
    }

    /**
     * Actualizar vehículo (via API)
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        return $this->updateViaApi(
            ApiController::class,
            $request,
            $vehicle,
            'admin.vehicles.show'
        );
    }

    /**
     * Eliminar vehículo (via API)
     */
    public function destroy(Vehicle $vehicle)
    {
        return $this->deleteViaApi(
            ApiController::class,
            $vehicle,
            'admin.vehicles.index'
        );
    }
}
