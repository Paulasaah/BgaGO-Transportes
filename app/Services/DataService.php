<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\Reservation;
use App\Models\Delivery;
use App\Models\User;
use App\Models\Branch;
use App\Enums\ReservationStatus;
use App\Enums\VehicleStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DataService
{
    // ==========================================
    // DASHBOARD
    // ==========================================
    
    public static function getDashboardStats(): array
    {
        \Log::info('[DataService] Ejecutando getDashboardStats()');
        try {
            return Cache::remember('dashboard_stats', 300, function () {
                // Reservas activas
                $reservasActivas = Reservation::whereIn('estado', [
                    ReservationStatus::Pendiente,
                    ReservationStatus::Confirmada,
                    ReservationStatus::Activa
                ])->count();

                $reservasAyer = Reservation::whereIn('estado', [
                    ReservationStatus::Pendiente,
                    ReservationStatus::Confirmada,
                    ReservationStatus::Activa
                ])->whereDate('created_at', today()->subDay())->count();

                // Domicilios hoy
                $domiciliosHoy = Delivery::whereDate('created_at', today())->count();
                $domiciliosEnProgreso = Delivery::whereIn('estado', ['pendiente', 'en_camino'])->count();

                // Ingresos del mes
                $ingresosMes = Reservation::whereMonth('created_at', now()->month)
                    ->where('estado', ReservationStatus::Completada)
                    ->sum('monto_final');

                $ingresosMesAnterior = Reservation::whereMonth('created_at', now()->subMonth()->month)
                    ->where('estado', ReservationStatus::Completada)
                    ->sum('monto_final');

                // Vehículos en mantenimiento
                $vehiculosMantenimiento = Vehicle::where('estado', VehicleStatus::Mantenimiento)->count();
                $vehiculosAtencion = Vehicle::where('estado', VehicleStatus::Mantenimiento)
                    ->whereHas('maintenances', fn($q) => $q->where('estado', 'pendiente'))
                    ->count();

                return [
                    'reservas_activas' => [
                        'value' => $reservasActivas,
                        'change' => $reservasActivas - $reservasAyer >= 0 
                            ? '+' . ($reservasActivas - $reservasAyer) 
                            : ($reservasActivas - $reservasAyer),
                        'change_text' => 'desde ayer',
                        'change_type' => $reservasActivas >= $reservasAyer ? 'positive' : 'negative'
                    ],
                    'domicilios_hoy' => [
                        'value' => $domiciliosHoy,
                        'change' => '+' . $domiciliosEnProgreso,
                        'change_text' => 'en progreso',
                        'change_type' => 'positive'
                    ],
                    'ingresos_mes' => [
                        'value' => '$' . number_format($ingresosMes / 1000000, 1) . 'M',
                        'change' => $ingresosMesAnterior > 0 
                            ? '+' . round((($ingresosMes - $ingresosMesAnterior) / $ingresosMesAnterior) * 100, 1) . '%'
                            : '+100%',
                        'change_text' => 'vs mes anterior',
                        'change_type' => $ingresosMes >= $ingresosMesAnterior ? 'positive' : 'negative'
                    ],
                    'en_mantenimiento' => [
                        'value' => $vehiculosMantenimiento,
                        'change' => $vehiculosAtencion,
                        'change_text' => 'requieren atención',
                        'change_type' => 'neutral'
                    ]
                ];
            });
        } catch (\Exception $e) {
            \Log::error('Error en getDashboardStats: ' . $e->getMessage());
            return [];
        }
    }

    public static function getReservasPorMes(): array
    {
        \Log::info('[DataService] Ejecutando getReservasPorMes()');
        try {
            $reservas = Reservation::select(
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

            $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            $data = array_fill(0, 12, 0);

            foreach ($reservas as $reserva) {
                $data[$reserva->mes - 1] = $reserva->total;
            }

            return [
                'labels' => $meses,
                'data' => $data
            ];
        } catch (\Exception $e) {
            \Log::error('Error en getReservasPorMes: ' . $e->getMessage());
            return [];
        }
    }

    public static function getDistribucionSedes(): array
    {
        \Log::info('[DataService] Ejecutando getDistribucionSedes()');
        try {
            $distribucion = Reservation::join('branches', 'reservations.sede_id', '=', 'branches.id')
                ->select('branches.nombre', DB::raw('COUNT(*) as total'))
                ->groupBy('branches.id', 'branches.nombre')
                ->get();

            return [
                'labels' => $distribucion->pluck('nombre')->toArray(),
                'data' => $distribucion->pluck('total')->toArray()
            ];
        } catch (\Exception $e) {
            \Log::error('Error en getDistribucionSedes: ' . $e->getMessage());
            return [];
        }
    }

    // ==========================================
    // MONITOREO
    // ==========================================
    
    public static function getLiveServices(): array
    {
        \Log::info('[DataService] Ejecutando getLiveServices()');
        try {
            return Reservation::with(['user:id,name', 'driver:id,name', 'vehicle:id,placa'])
                ->where('estado', ReservationStatus::Activa)
                ->whereDate('fecha_inicio', today())
                ->get()
                ->map(function ($reserva) {
                    return [
                        'id' => $reserva->id,
                        'codigo' => $reserva->codigo,
                        'tipo' => $reserva->tipo->value,
                        'usuario' => $reserva->user->name,
                        'conductor' => $reserva->driver?->name ?? 'Sin asignar',
                        'vehiculo' => $reserva->vehicle?->placa ?? 'Sin asignar',
                        'origen' => $reserva->origen_direccion,
                        'destino' => $reserva->destino_direccion ?? 'Sin destino',
                        'hora_inicio' => $reserva->fecha_inicio->format('H:i'),
                        'eta' => $reserva->fecha_fin->format('H:i'),
                        'progreso' => self::calcularProgreso($reserva),
                        'distancia_restante' => $reserva->distancia_km ? round($reserva->distancia_km, 1) . ' km' : 'N/A',
                        'status' => 'en_ruta',
                        'prioridad' => self::determinarPrioridad($reserva)
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            \Log::error('Error en getLiveServices: ' . $e->getMessage());
            return [];
        }
    }

    public static function getVehicleStatusSummary(): array
    {
        \Log::info('[DataService] Ejecutando getVehicleStatusSummary()');
        try {
            $disponibles = Vehicle::where('estado', VehicleStatus::Disponible)->limit(3)->pluck('placa')->toArray();
            $enServicio = Vehicle::where('estado', VehicleStatus::Ocupado)->limit(3)->pluck('placa')->toArray();
            $mantenimiento = Vehicle::where('estado', VehicleStatus::Mantenimiento)->limit(3)->pluck('placa')->toArray();

            return [
                'disponibles' => [
                    'count' => Vehicle::where('estado', VehicleStatus::Disponible)->count(),
                    'vehiculos' => $disponibles
                ],
                'en_servicio' => [
                    'count' => Vehicle::where('estado', VehicleStatus::Ocupado)->count(),
                    'vehiculos' => $enServicio
                ],
                'mantenimiento' => [
                    'count' => Vehicle::where('estado', VehicleStatus::Mantenimiento)->count(),
                    'vehiculos' => $mantenimiento
                ]
            ];
        } catch (\Exception $e) {
            \Log::error('Error en getVehicleStatusSummary: ' . $e->getMessage());
            return [];
        }
    }

    // ==========================================
    // REPORTES
    // ==========================================
    
    public static function getRevenueReport(string $periodo = 'mes'): array
    {
        \Log::info("[DataService] Ejecutando getRevenueReport($periodo)");
        return Cache::remember("revenue_report_{$periodo}", 600, function () use ($periodo) {
            try {
                $fechaInicio = match($periodo) {
                    'mes' => now()->startOfMonth(),
                    'trimestre' => now()->startOfQuarter(),
                    'año' => now()->startOfYear(),
                    default => now()->startOfMonth()
                };

                $total = Reservation::where('estado', ReservationStatus::Completada)
                    ->where('created_at', '>=', $fechaInicio)
                    ->sum('monto_final');

                $porTipo = Reservation::select('tipo', DB::raw('SUM(monto_final) as total'))
                    ->where('estado', ReservationStatus::Completada)
                    ->where('created_at', '>=', $fechaInicio)
                    ->groupBy('tipo')
                    ->get();

                $totalGeneral = $porTipo->sum('total');

                $porDia = Reservation::select(
                        DB::raw('DATE(fecha_inicio) as fecha'),
                        DB::raw('SUM(monto_final) as monto')
                    )
                    ->where('estado', ReservationStatus::Completada)
                    ->where('created_at', '>=', $fechaInicio)
                    ->groupBy('fecha')
                    ->orderBy('fecha')
                    ->get()
                    ->toArray();

                return [
                    'total' => $total,
                    'cambio_porcentual' => self::calculatePercentageChange($total, $fechaInicio),
                    'por_tipo' => $porTipo->map(function ($item) use ($totalGeneral) {
                        return [
                            'tipo' => ucfirst($item->tipo->value),
                            'monto' => $item->total,
                            'porcentaje' => $totalGeneral > 0 
                                ? round(($item->total / $totalGeneral) * 100) 
                                : 0
                        ];
                    })->toArray(),
                    'por_dia' => $porDia
                ];
            } catch (\Exception $e) {
                \Log::error('Error en getRevenueReport: ' . $e->getMessage());
                return [];
            }
        });
    }

    public static function getVehicleUsageReport(): array
    {
        \Log::info('[DataService] Ejecutando getVehicleUsageReport()');
        return Cache::remember('vehicle_usage_report', 600, function () {
            try {
                return Vehicle::withCount(['reservations' => function ($query) {
                        $query->where('estado', ReservationStatus::Completada)
                            ->whereMonth('created_at', now()->month);
                    }])
                    ->with(['reservations' => function ($query) {
                        $query->where('estado', ReservationStatus::Completada)
                            ->whereMonth('created_at', now()->month);
                    }])
                    ->get()
                    ->map(function ($vehicle) {
                        $horasUso = $vehicle->reservations->sum(fn($r) => ($r->duracion_minutos ?? 0) / 60);
                        $tasaOcupacion = self::calcularTasaOcupacion($vehicle);

                        return [
                            'vehiculo' => "{$vehicle->placa} - {$vehicle->marca} {$vehicle->modelo}",
                            'servicios' => $vehicle->reservations_count,
                            'horas_uso' => round($horasUso, 1),
                            'ingresos' => $vehicle->reservations->sum('monto_final'),
                            'tasa_ocupacion' => $tasaOcupacion
                        ];
                    })
                    ->toArray();
            } catch (\Exception $e) {
                \Log::error('Error en getVehicleUsageReport: ' . $e->getMessage());
                return [];
            }
        });
    }

    public static function getDriverPerformance(): array
    {
        \Log::info('[DataService] Ejecutando getDriverPerformance()');
        return Cache::remember('driver_performance', 600, function () {
            try {
                return User::role('conductor')
                    ->withCount(['conductorReservations' => function ($query) {
                        $query->where('estado', ReservationStatus::Completada)
                            ->whereMonth('created_at', now()->month);
                    }])
                    ->with(['conductorReservations' => function ($query) {
                        $query->where('estado', ReservationStatus::Completada)
                            ->whereMonth('created_at', now()->month);
                    }])
                    ->get()
                    ->map(function ($driver) {
                        $horasTrabajo = $driver->conductorReservations->sum(fn($r) => ($r->duracion_minutos ?? 0) / 60);
                        $calificacion = $driver->conductorReservations
                            ->whereNotNull('calificacion_conductor')
                            ->avg('calificacion_conductor');

                        return [
                            'conductor' => $driver->name,
                            'servicios_completados' => $driver->conductor_reservations_count,
                            'calificacion_promedio' => round($calificacion ?? 0, 1),
                            'horas_trabajo' => round($horasTrabajo, 1),
                            'ingresos_generados' => $driver->conductorReservations->sum('monto_final')
                        ];
                    })
                    ->toArray();
            } catch (\Exception $e) {
                \Log::error('Error en getDriverPerformance: ' . $e->getMessage());
                return [];
            }
        });
    }

    // ==========================================
    // HELPERS PRIVADOS
    // ==========================================
    
    private static function calcularProgreso(Reservation $reserva): int
    {
        if (!$reserva->fecha_inicio_real) return 0;

        $totalMinutos = $reserva->fecha_inicio_real->diffInMinutes($reserva->fecha_fin);
        $minutosTranscurridos = $reserva->fecha_inicio_real->diffInMinutes(now());
        if ($totalMinutos <= 0) return 0;

        return min(100, (int) (($minutosTranscurridos / $totalMinutos) * 100));
    }

    private static function determinarPrioridad(Reservation $reserva): string
    {
        return self::calcularProgreso($reserva) > 80 ? 'alta' : 'normal';
    }

    private static function calcularTasaOcupacion(Vehicle $vehicle): int
    {
        try {
            $availabilityService = app(\App\Services\VehicleAvailabilityService::class);
            $result = $availabilityService->getVehicleOccupancyRate($vehicle->id, 30);
            return $result['success'] ? (int) $result['data']['tasa_ocupacion'] : 0;
        } catch (\Exception $e) {
            \Log::error("Error calculando tasa ocupación: " . $e->getMessage());
            return 0;
        }
    }

    private static function calculatePercentageChange(float $currentTotal, Carbon $fechaInicio): int
    {
        $diasPeriodo = now()->diffInDays($fechaInicio);
        $fechaInicioAnterior = $fechaInicio->copy()->subDays($diasPeriodo);
        
        $totalAnterior = Reservation::where('estado', ReservationStatus::Completada)
            ->whereBetween('created_at', [$fechaInicioAnterior, $fechaInicio])
            ->sum('monto_final');

        if ($totalAnterior == 0) {
            return $currentTotal > 0 ? 100 : 0;
        }

        return (int) round((($currentTotal - $totalAnterior) / $totalAnterior) * 100);
    }


    // ESTADÍSTICAS DE ADMINISTRACIÓN

    /**
     * Estadísticas de vehículos (total, activos, mantenimiento)
     */
    public static function getVehicleStats(): array
    {
        $total = \App\Models\Vehicle::count();
        $disponibles = \App\Models\Vehicle::where('estado', 'disponible')->count();
        $ocupados = \App\Models\Vehicle::where('estado', 'ocupado')->count();
        $mantenimiento = \App\Models\Vehicle::where('estado', 'mantenimiento')->count();

        return [
            'total' => $total,
            'disponibles' => $disponibles,
            'ocupados' => $ocupados,
            'mantenimiento' => $mantenimiento,
        ];
    }

    /**
     * Estadísticas de conductores
     */
    public static function getDriverStats(): array
    {
        $total = \App\Models\User::role('conductor')->count();
        $activos = \App\Models\User::role('conductor')->where('estado', 'activo')->count();
        $inactivos = \App\Models\User::role('conductor')->where('estado', 'inactivo')->count();

        return [
            'total' => $total,
            'activos' => $activos,
            'inactivos' => $inactivos,
        ];
    }

    /**
     * Estadísticas de reservas
     */
    public static function getReservationStats(): array
    {
        $total = \App\Models\Reservation::count();
        $activas = \App\Models\Reservation::whereIn('estado', ['pendiente', 'confirmada', 'activa'])->count();
        $completadas = \App\Models\Reservation::where('estado', 'completada')->count();
        $canceladas = \App\Models\Reservation::where('estado', 'cancelada')->count();

        return [
            'total' => $total,
            'activas' => $activas,
            'completadas' => $completadas,
            'canceladas' => $canceladas,
        ];
    }

    /**
     * Estadísticas de usuarios (clientes)
     */
    public static function getUserStats(): array
    {
        $total = \App\Models\User::count();
        $clientes = \App\Models\User::role('cliente')->count();
        $conductores = \App\Models\User::role('conductor')->count();
        $admins = \App\Models\User::role('admin')->count();

        return [
            'total' => $total,
            'clientes' => $clientes,
            'conductores' => $conductores,
            'admins' => $admins,
        ];
    }

    // ==========================================
    // LISTADOS ADMIN (para vistas index)
    // ==========================================

    public static function getUsers()
    {
        return \App\Models\User::with('roles')->orderBy('id', 'desc')->get();
    }

    public static function getDrivers()
    {
        return \App\Models\User::role('conductor')->with('roles')->orderBy('id', 'desc')->get();
    }

    public static function getVehicles()
    {
        return \App\Models\Vehicle::orderBy('id', 'desc')->get();
    }

    public static function getReservations()
    {
        return \App\Models\Reservation::with(['user', 'vehicle', 'driver'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener ubicaciones y estado de vehículos para monitoreo
     */
    public static function getVehicleLocations(): array
    {
        \Log::info('[DataService] Ejecutando getVehicleLocations()');
        
        try {
            return Vehicle::with(['driver:id,name', 'reservations' => function($q) {
                    $q->where('estado', ReservationStatus::Activa)
                      ->whereDate('fecha_inicio', today())
                      ->latest()
                      ->limit(1);
                }])
                ->get()
                ->map(function ($vehicle) {
                    $activeReservation = $vehicle->reservations->first();
                    
                    // Determinar status basado en estado del vehículo y reserva activa
                    $status = match($vehicle->estado) {
                        VehicleStatus::Ocupado => 'busy',
                        VehicleStatus::Disponible => 'available',
                        VehicleStatus::Mantenimiento => 'maintenance',
                        default => 'offline'
                    };

                    return [
                        'id' => $vehicle->id,
                        'placa' => $vehicle->placa,
                        'conductor' => $vehicle->driver?->name ?? null,
                        'status' => $status,
                        'velocidad' => $activeReservation ? rand(20, 60) : 0,
                        'rumbo' => $activeReservation ? self::getRandomDirection() : null,
                        'servicio_activo' => $activeReservation?->codigo ?? null,
                        'lat' => $activeReservation?->origen_lat ?? null,
                        'lng' => $activeReservation?->origen_lng ?? null,
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            \Log::error('Error en getVehicleLocations: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener alertas activas del sistema
     */
    public static function getActiveAlerts(): array
    {
        \Log::info('[DataService] Ejecutando getActiveAlerts()');
        
        try {
            $alerts = [];

            // Verificar vehículos en mantenimiento urgente
            $vehiculosMantenimiento = Vehicle::where('estado', VehicleStatus::Mantenimiento)
                ->whereHas('maintenances', function($q) {
                    $q->where('estado', 'pendiente')
                      ->where('fecha_programada', '<=', now()->addDays(3));
                })
                ->count();

            if ($vehiculosMantenimiento > 0) {
                $alerts[] = [
                    'id' => 'maint_' . time(),
                    'titulo' => 'Mantenimientos Programados',
                    'mensaje' => "{$vehiculosMantenimiento} vehículo(s) requieren mantenimiento en los próximos 3 días",
                    'severidad' => 'warning',
                    'timestamp' => now()->format('H:i')
                ];
            }

            // Verificar reservas sin conductor asignado
            $reservasSinConductor = Reservation::where('estado', ReservationStatus::Pendiente)
                ->whereNull('conductor_id')
                ->whereDate('fecha_inicio', '<=', now()->addDay())
                ->count();

            if ($reservasSinConductor > 0) {
                $alerts[] = [
                    'id' => 'driver_' . time(),
                    'titulo' => 'Reservas Sin Asignar',
                    'mensaje' => "{$reservasSinConductor} reserva(s) pendiente(s) sin conductor asignado",
                    'severidad' => 'error',
                    'timestamp' => now()->format('H:i')
                ];
            }

            // Verificar servicios con retraso
            $serviciosRetrasados = Reservation::where('estado', ReservationStatus::Activa)
                ->where('fecha_fin', '<', now())
                ->count();

            if ($serviciosRetrasados > 0) {
                $alerts[] = [
                    'id' => 'delay_' . time(),
                    'titulo' => 'Servicios Retrasados',
                    'mensaje' => "{$serviciosRetrasados} servicio(s) excedieron su tiempo estimado",
                    'severidad' => 'warning',
                    'timestamp' => now()->format('H:i')
                ];
            }

            return $alerts;

        } catch (\Exception $e) {
            \Log::error('Error en getActiveAlerts: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener eventos recientes para timeline
     */
    public static function getRecentEvents(int $limit = 10): array
    {
        \Log::info('[DataService] Ejecutando getRecentEvents()');
        
        try {
            $events = [];

            // Obtener reservas recientes
            $recentReservations = Reservation::with('user:id,name')
                ->whereIn('estado', [
                    ReservationStatus::Activa,
                    ReservationStatus::Confirmada,
                    ReservationStatus::Completada
                ])
                ->whereDate('created_at', today())
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            foreach ($recentReservations as $reserva) {
                $events[] = [
                    'id' => 'res_' . $reserva->id,
                    'titulo' => match($reserva->estado) {
                        ReservationStatus::Confirmada => 'Reserva Confirmada',
                        ReservationStatus::Activa => 'Servicio Iniciado',
                        ReservationStatus::Completada => 'Servicio Completado',
                        default => 'Nueva Reserva'
                    },
                    'descripcion' => "{$reserva->codigo} - {$reserva->user->name}",
                    'timestamp' => $reserva->created_at->toDateTimeString(),
                    'icono' => match($reserva->estado) {
                        ReservationStatus::Confirmada => 'check',
                        ReservationStatus::Activa => 'play',
                        ReservationStatus::Completada => 'flag',
                        default => 'user'
                    },
                    'color' => match($reserva->estado) {
                        ReservationStatus::Confirmada => 'blue',
                        ReservationStatus::Activa => 'orange',
                        ReservationStatus::Completada => 'green',
                        default => 'gray'
                    }
                ];
            }

            // Ordenar por timestamp
            usort($events, fn($a, $b) => strtotime($b['timestamp']) - strtotime($a['timestamp']));

            return array_slice($events, 0, $limit);

        } catch (\Exception $e) {
            \Log::error('Error en getRecentEvents: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener dirección cardinal aleatoria (helper)
     */
    private static function getRandomDirection(): string
    {
        $directions = ['N', 'NE', 'E', 'SE', 'S', 'SO', 'O', 'NO'];
        return $directions[array_rand($directions)];
    }


}