<?php

namespace App\Services;

use App\Models\User;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Reservation;
use App\Models\Sede;
use Illuminate\Support\Facades\DB;

/**
 * Servicio de datos con base de datos real
 * 
 * IMPORTANTE: Este archivo está preparado para cuando tengas tus modelos y migraciones.
 * Los métodos tienen la misma firma que MockDataService, solo cambia la implementación.
 */
class DataService
{
    // ==========================================
    // DASHBOARD
    // ==========================================
    
    public static function getDashboardStats(): array
    {
        return [
            'reservas_activas' => [
                'value' => Reservation::where('status', 'active')->count(),
                'change' => '+' . self::calculateChange('reservas', 'day'),
                'change_text' => 'desde ayer',
                'change_type' => 'positive'
            ],
            'domicilios_hoy' => [
                'value' => Reservation::where('tipo', 'domicilio')
                    ->whereDate('fecha_inicio', today())
                    ->count(),
                'change' => '+' . self::calculateChange('domicilios', 'day'),
                'change_text' => 'en progreso',
                'change_type' => 'positive'
            ],
            'ingresos_mes' => [
                'value' => '$' . number_format(
                    Reservation::whereMonth('created_at', now()->month)
                        ->sum('monto') / 1000000,
                    1
                ) . 'M',
                'change' => '+' . self::calculateChange('ingresos', 'month') . '%',
                'change_text' => 'vs mes anterior',
                'change_type' => 'positive'
            ],
            'en_mantenimiento' => [
                'value' => Vehicle::where('status', 'maintenance')->count(),
                'change' => Vehicle::where('status', 'maintenance')
                    ->where('requiere_atencion', true)
                    ->count(),
                'change_text' => 'requieren atención',
                'change_type' => 'neutral'
            ]
        ];
    }

    public static function getReservasPorMes(): array
    {
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
    }

    public static function getDistribucionSedes(): array
    {
        $distribucion = Reservation::join('sedes', 'reservations.sede_id', '=', 'sedes.id')
            ->select('sedes.nombre', DB::raw('COUNT(*) as total'))
            ->groupBy('sedes.id', 'sedes.nombre')
            ->get();

        return [
            'labels' => $distribucion->pluck('nombre')->toArray(),
            'data' => $distribucion->pluck('total')->toArray()
        ];
    }

    // ==========================================
    // MAPA
    // ==========================================
    
    public static function getVehicleLocations(): array
    {
        return Vehicle::with(['conductor', 'ubicacionActual'])
            ->whereIn('status', ['available', 'busy'])
            ->get()
            ->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'placa' => $vehicle->placa,
                    'conductor' => $vehicle->conductor?->name,
                    'lat' => $vehicle->ubicacionActual->latitud ?? 0,
                    'lng' => $vehicle->ubicacionActual->longitud ?? 0,
                    'status' => $vehicle->status,
                    'velocidad' => $vehicle->ubicacionActual->velocidad ?? 0,
                    'rumbo' => $vehicle->ubicacionActual->rumbo ?? 'N',
                    'servicio_activo' => $vehicle->reservaActiva?->codigo
                ];
            })
            ->toArray();
    }

    public static function getActiveRoutes(): array
    {
        return Reservation::with(['vehiculo.ubicacionActual', 'origen', 'destino'])
            ->where('status', 'active')
            ->whereNotNull('vehiculo_id')
            ->get()
            ->map(function ($reserva) {
                return [
                    'servicio_id' => $reserva->codigo,
                    'vehiculo_id' => $reserva->vehiculo_id,
                    'origen' => [
                        'lat' => $reserva->origen->latitud,
                        'lng' => $reserva->origen->longitud,
                        'nombre' => $reserva->origen->direccion
                    ],
                    'destino' => [
                        'lat' => $reserva->destino->latitud,
                        'lng' => $reserva->destino->longitud,
                        'nombre' => $reserva->destino->direccion
                    ],
                    'waypoints' => $reserva->waypoints ?? [],
                    'progreso' => $reserva->calcularProgreso()
                ];
            })
            ->toArray();
    }

    public static function getMapFilters(): array
    {
        return [
            'sedes' => Sede::pluck('nombre')->toArray(),
            'estados' => [
                'available' => 'Disponible',
                'busy' => 'Ocupado',
                'maintenance' => 'Mantenimiento'
            ]
        ];
    }

    // ==========================================
    // MONITOREO
    // ==========================================
    
    public static function getLiveServices(): array
    {
        return Reservation::with(['usuario', 'conductor', 'vehiculo'])
            ->where('status', 'active')
            ->whereDate('fecha_inicio', today())
            ->get()
            ->map(function ($reserva) {
                return [
                    'id' => $reserva->id,
                    'codigo' => $reserva->codigo,
                    'tipo' => $reserva->tipo,
                    'usuario' => $reserva->usuario->name,
                    'conductor' => $reserva->conductor?->name,
                    'vehiculo' => $reserva->vehiculo?->placa,
                    'origen' => $reserva->origen_direccion,
                    'destino' => $reserva->destino_direccion,
                    'hora_inicio' => $reserva->fecha_inicio->format('H:i'),
                    'eta' => $reserva->calcularETA(),
                    'progreso' => $reserva->calcularProgreso(),
                    'distancia_restante' => $reserva->calcularDistanciaRestante(),
                    'status' => $reserva->status,
                    'prioridad' => $reserva->prioridad
                ];
            })
            ->toArray();
    }

    public static function getVehicleStatusSummary(): array
    {
        return [
            'disponibles' => [
                'count' => Vehicle::where('status', 'available')->count(),
                'vehiculos' => Vehicle::where('status', 'available')
                    ->limit(3)
                    ->pluck('placa')
                    ->toArray()
            ],
            'en_servicio' => [
                'count' => Vehicle::where('status', 'busy')->count(),
                'vehiculos' => Vehicle::where('status', 'busy')
                    ->limit(3)
                    ->pluck('placa')
                    ->toArray()
            ],
            'mantenimiento' => [
                'count' => Vehicle::where('status', 'maintenance')->count(),
                'vehiculos' => Vehicle::where('status', 'maintenance')
                    ->limit(3)
                    ->pluck('placa')
                    ->toArray()
            ]
        ];
    }

    public static function getAlerts(): array
    {
        // Implementar lógica de alertas desde tabla 'alerts' o calcular dinámicamente
        return DB::table('alerts')
            ->where('leida', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public static function getEventTimeline(): array
    {
        // Implementar timeline de eventos desde tabla 'events'
        return DB::table('events')
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'tipo' => $event->tipo,
                    'icono' => self::getEventIcon($event->tipo),
                    'titulo' => $event->titulo,
                    'descripcion' => $event->descripcion,
                    'timestamp' => $event->created_at,
                    'color' => self::getEventColor($event->tipo)
                ];
            })
            ->toArray();
    }

    // ==========================================
    // CATÁLOGO
    // ==========================================
    
    public static function getCatalogVehicles(array $filters = []): array
    {
        $query = Vehicle::with(['imagenes', 'caracteristicas', 'sede'])
            ->where('visible_catalogo', true);

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['disponible'])) {
            $query->where('status', 'available');
        }

        if (!empty($filters['capacidad'])) {
            $query->where('capacidad', '>=', $filters['capacidad']);
        }

        if (!empty($filters['precio_max'])) {
            $query->where('precio_dia', '<=', $filters['precio_max']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%")
                  ->orWhere('modelo', 'like', "%{$search}%");
            });
        }

        return $query->get()->map(function ($vehicle) {
            return [
                'id' => $vehicle->id,
                'nombre' => $vehicle->nombre,
                'placa' => $vehicle->placa,
                'tipo' => $vehicle->tipo,
                'marca' => $vehicle->marca,
                'modelo' => $vehicle->modelo,
                'year' => $vehicle->year,
                'capacidad' => $vehicle->capacidad,
                'precio_hora' => $vehicle->precio_hora,
                'precio_dia' => $vehicle->precio_dia,
                'caracteristicas' => $vehicle->caracteristicas->pluck('nombre')->toArray(),
                'imagenes' => $vehicle->imagenes->pluck('url')->toArray(),
                'disponible' => $vehicle->status === 'available',
                'calificacion' => $vehicle->calificacion_promedio,
                'num_reviews' => $vehicle->reviews_count,
                'sede' => $vehicle->sede->nombre
            ];
        })->toArray();
    }

    public static function getVehicleById(int $id): ?array
    {
        $vehicle = Vehicle::with(['imagenes', 'caracteristicas', 'sede', 'reviews'])
            ->find($id);

        if (!$vehicle) {
            return null;
        }

        return [
            'id' => $vehicle->id,
            'nombre' => $vehicle->nombre,
            'placa' => $vehicle->placa,
            'tipo' => $vehicle->tipo,
            'marca' => $vehicle->marca,
            'modelo' => $vehicle->modelo,
            'year' => $vehicle->year,
            'capacidad' => $vehicle->capacidad,
            'precio_hora' => $vehicle->precio_hora,
            'precio_dia' => $vehicle->precio_dia,
            'caracteristicas' => $vehicle->caracteristicas->pluck('nombre')->toArray(),
            'imagenes' => $vehicle->imagenes->pluck('url')->toArray(),
            'disponible' => $vehicle->status === 'available',
            'calificacion' => $vehicle->calificacion_promedio,
            'num_reviews' => $vehicle->reviews_count,
            'sede' => $vehicle->sede->nombre
        ];
    }

    public static function getVehicleAvailability(int $vehicleId, string $fecha): array
    {
        $reservas = Reservation::where('vehiculo_id', $vehicleId)
            ->whereDate('fecha_inicio', $fecha)
            ->get();

        $slots = [];
        for ($hora = 8; $hora <= 18; $hora++) {
            $horaStr = str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00';
            $disponible = true;

            foreach ($reservas as $reserva) {
                if ($reserva->horaOcupada($horaStr)) {
                    $disponible = false;
                    break;
                }
            }

            $slots[] = [
                'hora' => $horaStr,
                'disponible' => $disponible
            ];
        }

        return [
            'fecha' => $fecha,
            'slots' => $slots
        ];
    }

    public static function getUserReservations(int $userId): array
    {
        return Reservation::with(['vehiculo'])
            ->where('user_id', $userId)
            ->orderBy('fecha_inicio', 'desc')
            ->get()
            ->map(function ($reserva) {
                return [
                    'id' => $reserva->id,
                    'codigo' => $reserva->codigo,
                    'vehiculo' => $reserva->vehiculo->nombre,
                    'vehiculo_imagen' => $reserva->vehiculo->imagen_principal,
                    'fecha_inicio' => $reserva->fecha_inicio->format('Y-m-d H:i'),
                    'fecha_fin' => $reserva->fecha_fin->format('Y-m-d H:i'),
                    'origen' => $reserva->origen_direccion,
                    'destino' => $reserva->destino_direccion,
                    'monto' => $reserva->monto,
                    'status' => $reserva->status,
                    'puede_cancelar' => $reserva->puedeCancelar()
                ];
            })
            ->toArray();
    }

    // ==========================================
    // REPORTES
    // ==========================================
    
    public static function getRevenueReport(string $periodo = 'mes'): array
    {
        $fechaInicio = $periodo === 'mes' 
            ? now()->startOfMonth() 
            : now()->startOfYear();

        $total = Reservation::where('status', 'completed')
            ->where('created_at', '>=', $fechaInicio)
            ->sum('monto');

        $porTipo = Reservation::select('tipo', DB::raw('SUM(monto) as total'))
            ->where('status', 'completed')
            ->where('created_at', '>=', $fechaInicio)
            ->groupBy('tipo')
            ->get();

        $totalGeneral = $porTipo->sum('total');

        return [
            'total' => $total,
            'cambio_porcentual' => self::calculateChange('ingresos', $periodo),
            'por_tipo' => $porTipo->map(function ($item) use ($totalGeneral) {
                return [
                    'tipo' => ucfirst($item->tipo),
                    'monto' => $item->total,
                    'porcentaje' => $totalGeneral > 0 
                        ? round(($item->total / $totalGeneral) * 100) 
                        : 0
                ];
            })->toArray(),
            'por_dia' => self::getRevenueByDay($fechaInicio)
        ];
    }

    public static function getVehicleUsageReport(): array
    {
        return Vehicle::withCount('reservations')
            ->with(['reservations' => function ($query) {
                $query->where('status', 'completed')
                    ->whereMonth('created_at', now()->month);
            }])
            ->get()
            ->map(function ($vehicle) {
                $horasUso = $vehicle->reservations->sum(function ($reserva) {
                    return $reserva->fecha_inicio->diffInHours($reserva->fecha_fin);
                });

                return [
                    'vehiculo' => "{$vehicle->placa} - {$vehicle->marca} {$vehicle->modelo}",
                    'servicios' => $vehicle->reservations_count,
                    'horas_uso' => $horasUso,
                    'ingresos' => $vehicle->reservations->sum('monto'),
                    'tasa_ocupacion' => $vehicle->calcularTasaOcupacion()
                ];
            })
            ->toArray();
    }

    public static function getDriverPerformance(): array
    {
        return Driver::withCount(['reservations' => function ($query) {
                $query->where('status', 'completed')
                    ->whereMonth('created_at', now()->month);
            }])
            ->with(['reservations' => function ($query) {
                $query->where('status', 'completed')
                    ->whereMonth('created_at', now()->month);
            }])
            ->get()
            ->map(function ($driver) {
                $horasTrabajo = $driver->reservations->sum(function ($reserva) {
                    return $reserva->fecha_inicio->diffInHours($reserva->fecha_fin);
                });

                return [
                    'conductor' => $driver->name,
                    'servicios_completados' => $driver->reservations_count,
                    'calificacion_promedio' => $driver->calificacion_promedio,
                    'horas_trabajo' => $horasTrabajo,
                    'ingresos_generados' => $driver->reservations->sum('monto')
                ];
            })
            ->toArray();
    }

    // ==========================================
    // USUARIOS
    // ==========================================
    
    public static function getUserStats(): array
    {
        return [
            'total' => User::count(),
            'activos' => User::where('status', 'active')->count(),
            'nuevos_mes' => User::whereMonth('created_at', now()->month)->count(),
            'inactivos' => User::where('status', 'inactive')->count()
        ];
    }

    public static function getUsers(array $filters = []): array
    {
        $query = User::with(['sede']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => $user->status,
                'reservas_count' => $user->reservations_count,
                'ultima_reserva' => $user->ultima_reserva?->format('Y-m-d'),
                'registered_at' => $user->created_at->format('Y-m-d'),
                'sede' => $user->sede?->nombre
            ];
        })->toArray();
    }

    // ==========================================
    // CONDUCTORES
    // ==========================================
    
    public static function getDriverStats(): array
    {
        return [
            'total' => Driver::count(),
            'disponibles' => Driver::where('status', 'available')->count(),
            'en_servicio' => Driver::where('status', 'busy')->count(),
            'inactivos' => Driver::where('status', 'inactive')->count()
        ];
    }

    public static function getDrivers(array $filters = []): array
    {
        $query = Driver::with(['vehiculoAsignado', 'sede']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('license', 'like', "%{$search}%");
            });
        }

        return $query->get()->map(function ($driver) {
            return [
                'id' => $driver->id,
                'name' => $driver->name,
                'email' => $driver->email,
                'phone' => $driver->phone,
                'license' => $driver->license,
                'status' => $driver->status,
                'vehiculo_asignado' => $driver->vehiculoAsignado?->placa,
                'servicios_completados' => $driver->servicios_completados,
                'calificacion' => $driver->calificacion_promedio,
                'ultima_actividad' => $driver->ultima_actividad?->format('Y-m-d H:i'),
                'sede' => $driver->sede?->nombre
            ];
        })->toArray();
    }

    // ==========================================
    // VEHÍCULOS
    // ==========================================
    
    public static function getVehicleStats(): array
    {
        return [
            'total' => Vehicle::count(),
            'disponibles' => Vehicle::where('status', 'available')->count(),
            'en_servicio' => Vehicle::where('status', 'busy')->count(),
            'mantenimiento' => Vehicle::where('status', 'maintenance')->count()
        ];
    }

    public static function getVehicles(array $filters = []): array
    {
        $query = Vehicle::with(['conductorAsignado', 'sede']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('placa', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%")
                  ->orWhere('modelo', 'like', "%{$search}%");
            });
        }

        return $query->get()->map(function ($vehicle) {
            return [
                'id' => $vehicle->id,
                'placa' => $vehicle->placa,
                'marca' => $vehicle->marca,
                'modelo' => $vehicle->modelo,
                'year' => $vehicle->year,
                'tipo' => $vehicle->tipo,
                'capacidad' => $vehicle->capacidad,
                'status' => $vehicle->status,
                'conductor_asignado' => $vehicle->conductorAsignado?->name,
                'kilometraje' => $vehicle->kilometraje,
                'ultimo_mantenimiento' => $vehicle->ultimo_mantenimiento?->format('Y-m-d'),
                'proximo_mantenimiento' => $vehicle->proximo_mantenimiento?->format('Y-m-d'),
                'sede' => $vehicle->sede?->nombre
            ];
        })->toArray();
    }

    // ==========================================
    // RESERVAS
    // ==========================================
    
    public static function getReservationStats(): array
    {
        return [
            'pendientes' => Reservation::where('status', 'pending')->count(),
            'activas' => Reservation::where('status', 'active')->count(),
            'completadas_hoy' => Reservation::where('status', 'completed')
                ->whereDate('fecha_fin', today())
                ->count(),
            'canceladas_mes' => Reservation::where('status', 'cancelled')
                ->whereMonth('created_at', now()->month)
                ->count()
        ];
    }

    public static function getReservations(array $filters = []): array
    {
        $query = Reservation::with(['usuario', 'conductor', 'vehiculo', 'sede']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhereHas('usuario', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('fecha_inicio', 'desc')
            ->get()
            ->map(function ($reserva) {
                return [
                    'id' => $reserva->id,
                    'codigo' => $reserva->codigo,
                    'usuario' => $reserva->usuario->name,
                    'conductor' => $reserva->conductor?->name,
                    'vehiculo' => $reserva->vehiculo ? 
                        "{$reserva->vehiculo->placa} - {$reserva->vehiculo->marca} {$reserva->vehiculo->modelo}" : 
                        null,
                    'origen' => $reserva->origen_direccion,
                    'destino' => $reserva->destino_direccion,
                    'fecha_inicio' => $reserva->fecha_inicio->format('Y-m-d H:i'),
                    'fecha_fin' => $reserva->fecha_fin->format('Y-m-d H:i'),
                    'tipo' => $reserva->tipo,
                    'status' => $reserva->status,
                    'monto' => $reserva->monto,
                    'sede' => $reserva->sede?->nombre
                ];
            })
            ->toArray();
    }

    // ==========================================
    // HELPERS PRIVADOS
    // ==========================================
    
    private static function calculateChange(string $type, string $period): int
    {
        // Implementar lógica de cálculo de cambios
        return 0;
    }

    private static function getRevenueByDay($fechaInicio): array
    {
        return Reservation::select(
                DB::raw('DATE(fecha_inicio) as fecha'),
                DB::raw('SUM(monto) as monto')
            )
            ->where('status', 'completed')
            ->where('created_at', '>=', $fechaInicio)
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->toArray();
    }

    private static function getEventIcon(string $tipo): string
    {
        $icons = [
            'servicio_iniciado' => 'play',
            'servicio_completado' => 'check',
            'conductor_disponible' => 'user',
            'mantenimiento' => 'wrench'
        ];

        return $icons[$tipo] ?? 'circle';
    }

    private static function getEventColor(string $tipo): string
    {
        $colors = [
            'servicio_iniciado' => 'blue',
            'servicio_completado' => 'green',
            'conductor_disponible' => 'gray',
            'mantenimiento' => 'orange'
        ];

        return $colors[$tipo] ?? 'gray';
    }

    // ==========================================
    // HELPERS PÚBLICOS (mantener compatibilidad)
    // ==========================================
    
    public static function getStatusLabel(string $status): string
    {
        return MockDataService::getStatusLabel($status);
    }

    public static function getStatusColor(string $status): string
    {
        return MockDataService::getStatusColor($status);
    }

    public static function getSeverityColor(string $severity): string
    {
        return MockDataService::getSeverityColor($severity);
    }
}