<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DataService;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = DataService::getDashboardStats();
        $reservasPorMes = DataService::getReservasPorMes();
        $distribucionSedes = DataService::getDistribucionSedes();

        return view('admin.dashboard.index', compact('stats', 'reservasPorMes', 'distribucionSedes'));
    }
}
