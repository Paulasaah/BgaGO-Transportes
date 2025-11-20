<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DataService;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = DataService::getDashboardStats();
        $reservas = DataService::getReservasPorMes();
        $distribucion = DataService::getDistribucionSedes();

        $reservasLabels = $reservas['labels'] ?? [];
        $reservasData = $reservas['data'] ?? [];

        $distribucionLabels = $distribucion['labels'] ?? [];
        $distribucionData = $distribucion['data'] ?? [];

        return view('livewire.dashboard.simple-dashboard', compact(
            'stats',
            'reservasLabels',
            'reservasData',
            'distribucionLabels',
            'distribucionData'
        ));
    }
}
