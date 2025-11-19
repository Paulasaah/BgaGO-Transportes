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

                // Domicilios en progreso (no finalizados): pendientes, asignados o en camino
                $domiciliosEnProgreso = Delivery::whereIn('estado', [
                    'pendiente',
                    'asignado',
                    'en_camino',
                ])->count();

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
            return ['labels' => [], 'data' => []];
        }
    }

    public static function getDistribucionSedes(): array
    {
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
            return ['labels' => [], 'data' => []];
        }
    }

    // ==========================================
    // MONITOREO
    // ==========================================
    
    public static function getLiveServices(): array
    {
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

    public static function getVehicleLocations(): array
    {
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

    public static function getActiveAlerts(): array
    {
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

    public static function getRecentEvents(int $limit = 10): array
    {
        try {
            $events = [];

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

            usort($events, fn($a, $b) => strtotime($b['timestamp']) - strtotime($a['timestamp']));

            return array_slice($events, 0, $limit);

        } catch (\Exception $e) {
            \Log::error('Error en getRecentEvents: ' . $e->getMessage());
            return [];
        }
    }

    // ==========================================
    // REPORTES
    // ==========================================
    
    public static function getRevenueReport(string $periodo = 'mes'): array
    {
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
                ->map(function ($item) {
                    return [
                        'fecha' => $item->fecha,
                        'monto' => (float) $item->monto
                    ];
                })
                ->toArray();

            $diasPeriodo = now()->diffInDays($fechaInicio);
            $fechaInicioAnterior = $fechaInicio->copy()->subDays($diasPeriodo);
            
            $totalAnterior = Reservation::where('estado', ReservationStatus::Completada)
                ->whereBetween('created_at', [$fechaInicioAnterior, $fechaInicio])
                ->sum('monto_final');

            $cambioPortentual = 0;
            if ($totalAnterior > 0) {
                $cambioPortentual = (int) round((($total - $totalAnterior) / $totalAnterior) * 100);
            } elseif ($total > 0) {
                $cambioPortentual = 100;
            }

            return [
                'total' => (float) $total,
                'cambio_porcentual' => $cambioPortentual,
                'por_tipo' => $porTipo->map(function ($item) use ($totalGeneral) {
                    return [
                        'tipo' => ucfirst($item->tipo->value ?? $item->tipo),
                        'monto' => (float) $item->total,
                        'porcentaje' => $totalGeneral > 0 
                            ? round(($item->total / $totalGeneral) * 100) 
                            : 0
                    ];
                })->toArray(),
                'por_dia' => $porDia
            ];
        } catch (\Exception $e) {
            \Log::error('Error en getRevenueReport: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return [
                'total' => 0,
                'cambio_porcentual' => 0,
                'por_tipo' => [],
                'por_dia' => []
            ];
        }
    }

    public static function getVehicleUsageReport(): array
    {
        try {
            $vehicles = Vehicle::with(['reservations' => function ($query) {
                    $query->where('estado', ReservationStatus::Completada)
                        ->whereMonth('created_at', now()->month);
                }])
                ->get();

            return $vehicles->map(function ($vehicle) {
                $reservations = $vehicle->reservations;
                $servicios = $reservations->count();
                
                $horasUso = $reservations->sum(function($r) {
                    return ($r->duracion_minutos ?? 0) / 60;
                });
                
                $ingresos = $reservations->sum('monto_final');
                
                $diasConServicio = $reservations
                    ->pluck('fecha_inicio')
                    ->map(fn($fecha) => $fecha->format('Y-m-d'))
                    ->unique()
                    ->count();
                    
                $diasDelMes = now()->daysInMonth;
                $tasaOcupacion = $diasDelMes > 0 
                    ? round(($diasConServicio / $diasDelMes) * 100) 
                    : 0;

                return [
                    'vehiculo' => "{$vehicle->placa} - {$vehicle->marca} {$vehicle->modelo}",
                    'servicios' => $servicios,
                    'horas_uso' => round($horasUso, 1),
                    'ingresos' => (float) $ingresos,
                    'tasa_ocupacion' => $tasaOcupacion
                ];
            })
            ->sortByDesc('servicios')
            ->values()
            ->toArray();

        } catch (\Exception $e) {
            \Log::error('Error en getVehicleUsageReport: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return [];
        }
    }

    public static function getDriverPerformance(): array
    {
        try {
            $drivers = User::role('conductor')
                ->with(['conductorReservations' => function ($query) {
                    $query->where('estado', ReservationStatus::Completada)
                        ->whereMonth('created_at', now()->month);
                }])
                ->get();

            return $drivers->map(function ($driver) {
                $reservations = $driver->conductorReservations;
                $serviciosCompletados = $reservations->count();
                
                $horasTrabajo = $reservations->sum(function($r) {
                    return ($r->duracion_minutos ?? 0) / 60;
                });
                
                $calificaciones = $reservations
                    ->whereNotNull('calificacion_conductor')
                    ->pluck('calificacion_conductor');
                    
                $calificacionPromedio = $calificaciones->count() > 0 
                    ? $calificaciones->avg() 
                    : 0;
                
                $ingresosGenerados = $reservations->sum('monto_final');

                return [
                    'conductor' => $driver->name,
                    'servicios_completados' => $serviciosCompletados,
                    'calificacion_promedio' => round($calificacionPromedio, 1),
                    'horas_trabajo' => round($horasTrabajo, 1),
                    'ingresos_generados' => (float) $ingresosGenerados
                ];
            })
            ->sortByDesc('servicios_completados')
            ->values()
            ->toArray();

        } catch (\Exception $e) {
            \Log::error('Error en getDriverPerformance: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return [];
        }
    }

    // ESTADÍSTICAS ADMINISTRATIVAS

    public static function getVehicleStats(): array
    {
        try {
            $total = Vehicle::count();
            $disponibles = Vehicle::where('estado', VehicleStatus::Disponible)->count();
            $ocupados = Vehicle::where('estado', VehicleStatus::Ocupado)->count();
            $mantenimiento = Vehicle::where('estado', VehicleStatus::Mantenimiento)->count();

            return [
                'total' => $total,
                'disponibles' => $disponibles,
                'ocupados' => $ocupados,
                'mantenimiento' => $mantenimiento,
            ];
        } catch (\Exception $e) {
            \Log::error('Error en getVehicleStats: ' . $e->getMessage());
            return ['total' => 0, 'disponibles' => 0, 'ocupados' => 0, 'mantenimiento' => 0];
        }
    }

    public static function getDriverStats(): array
    {
        try {
            $total = User::role('conductor')->count();
            $activos = User::role('conductor')
                ->whereHas('driverProfile', fn($q) => $q->where('is_active', true))
                ->count();
            $inactivos = $total - $activos;

            return [
                'total' => $total,
                'activos' => $activos,
                'inactivos' => $inactivos,
            ];
        } catch (\Exception $e) {
            \Log::error('Error en getDriverStats: ' . $e->getMessage());
            return ['total' => 0, 'activos' => 0, 'inactivos' => 0];
        }
    }

    public static function getReservationStats(): array
    {
        try {
            $total = Reservation::count();
            $activas = Reservation::whereIn('estado', [
                ReservationStatus::Pendiente,
                ReservationStatus::Confirmada,
                ReservationStatus::Activa
            ])->count();
            
            $completadas = Reservation::where('estado', ReservationStatus::Completada)
                ->whereDate('created_at', today())
                ->count();
                
            $canceladas = Reservation::where('estado', ReservationStatus::Cancelada)
                ->whereMonth('created_at', now()->month)
                ->count();

            return [
                'total' => $total,
                'activas' => $activas,
                'completadas' => $completadas,
                'completadas_hoy' => $completadas,
                'canceladas' => $canceladas,
                'canceladas_mes' => $canceladas,
            ];
        } catch (\Exception $e) {
            \Log::error('Error en getReservationStats: ' . $e->getMessage());
            
            return [
                'total' => 0,
                'activas' => 0,
                'completadas' => 0,
                'completadas_hoy' => 0,
                'canceladas' => 0,
                'canceladas_mes' => 0,
            ];
        }
    }

    public static function getUserStats(): array
    {
        try {
            $total = User::count();
            $clientes = User::role('cliente')->count();
            $conductores = User::role('conductor')->count();
            $admins = User::role('admin')->count();

            return [
                'total' => $total,
                'clientes' => $clientes,
                'conductores' => $conductores,
                'admins' => $admins,
            ];
        } catch (\Exception $e) {
            \Log::error('Error en getUserStats: ' . $e->getMessage());
            return ['total' => 0, 'clientes' => 0, 'conductores' => 0, 'admins' => 0];
        }
    }

    // LISTADOS ADMIN

    public static function getUsers()
    {
        return User::with('roles')->orderBy('id', 'desc')->get();
    }

    public static function getDrivers()
    {
        return User::role('conductor')->with('roles')->orderBy('id', 'desc')->get();
    }

    public static function getVehicles()
    {
        return Vehicle::orderBy('id', 'desc')->get();
    }

    public static function getReservations()
    {
        return Reservation::with(['user', 'vehicle', 'driver'])
            ->orderBy('created_at', 'desc')
            ->get();
    }


    // HELPERS PRIVADOS
    
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

    private static function getRandomDirection(): string
    {
        $directions = ['N', 'NE', 'E', 'SE', 'S', 'SO', 'O', 'NO'];
        return $directions[array_rand($directions)];
    }
}