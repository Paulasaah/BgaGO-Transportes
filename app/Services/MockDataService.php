<?php

namespace App\Services;

/**
 * Servicio para proveer datos estáticos mientras no hay base de datos
 * 
 * MIGRACIÓN A BD:
 * 1. Crea DataService.php con la misma estructura
 * 2. Cambia los return por queries Eloquent
 * 3. Reemplaza MockDataService por DataService en los componentes
 */
class MockDataService
{
    // ==========================================
    // DASHBOARD
    // ==========================================
    
    public static function getDashboardStats(): array
    {
        return [
            'reservas_activas' => [
                'value' => 24,
                'change' => '+5',
                'change_text' => 'desde ayer',
                'change_type' => 'positive'
            ],
            'domicilios_hoy' => [
                'value' => 18,
                'change' => '+3',
                'change_text' => 'en progreso',
                'change_type' => 'positive'
            ],
            'ingresos_mes' => [
                'value' => '$4.2M',
                'change' => '+12%',
                'change_text' => 'vs mes anterior',
                'change_type' => 'positive'
            ],
            'en_mantenimiento' => [
                'value' => 5,
                'change' => '2',
                'change_text' => 'requieren atención',
                'change_type' => 'neutral'
            ]
        ];
    }

    public static function getReservasPorMes(): array
    {
        return [
            'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            'data' => [65, 78, 90, 81, 95, 103, 110, 98, 115, 122, 130, 140]
        ];
    }

    public static function getDistribucionSedes(): array
    {
        return [
            'labels' => ['Sede Norte', 'Sede Sur', 'Sede Centro', 'Sede Oriente'],
            'data' => [35, 28, 22, 15]
        ];
    }

    // ==========================================
    // MAPA - NUEVOS MÉTODOS
    // ==========================================
    
    public static function getVehicleLocations(): array
    {
        return [
            [
                'id' => 1,
                'placa' => 'ABC-123',
                'conductor' => 'Pedro Gómez',
                'lat' => 7.1193,
                'lng' => -73.1227,
                'status' => 'available',
                'velocidad' => 0,
                'rumbo' => 'N'
            ],
            [
                'id' => 2,
                'placa' => 'DEF-456',
                'conductor' => 'Jorge Ramírez',
                'lat' => 7.1301,
                'lng' => -73.1137,
                'status' => 'busy',
                'velocidad' => 45,
                'rumbo' => 'NE',
                'servicio_activo' => 'DOM-2024-045'
            ],
            [
                'id' => 4,
                'placa' => 'JKL-012',
                'conductor' => 'Roberto Díaz',
                'lat' => 7.1089,
                'lng' => -73.1198,
                'status' => 'available',
                'velocidad' => 0,
                'rumbo' => 'S'
            ],
            [
                'id' => 5,
                'placa' => 'MNO-345',
                'conductor' => 'Fernando López',
                'lat' => 7.1156,
                'lng' => -73.1096,
                'status' => 'busy',
                'velocidad' => 30,
                'rumbo' => 'E',
                'servicio_activo' => 'DOM-2024-046'
            ]
        ];
    }

    public static function getActiveRoutes(): array
    {
        return [
            [
                'servicio_id' => 'DOM-2024-045',
                'vehiculo_id' => 2,
                'origen' => ['lat' => 7.1301, 'lng' => -73.1137, 'nombre' => 'Centro Comercial Cacique'],
                'destino' => ['lat' => 7.0694, 'lng' => -73.1078, 'nombre' => 'Calle 30 #15-20, Girón'],
                'waypoints' => [
                    ['lat' => 7.1250, 'lng' => -73.1150],
                    ['lat' => 7.1100, 'lng' => -73.1120],
                    ['lat' => 7.0850, 'lng' => -73.1100]
                ],
                'progreso' => 45
            ],
            [
                'servicio_id' => 'DOM-2024-046',
                'vehiculo_id' => 5,
                'origen' => ['lat' => 7.1156, 'lng' => -73.1096, 'nombre' => 'Megamall'],
                'destino' => ['lat' => 7.1297, 'lng' => -73.1205, 'nombre' => 'Cabecera del Llano'],
                'waypoints' => [
                    ['lat' => 7.1200, 'lng' => -73.1150],
                    ['lat' => 7.1250, 'lng' => -73.1180]
                ],
                'progreso' => 65
            ]
        ];
    }

    public static function getMapFilters(): array
    {
        return [
            'sedes' => ['Norte', 'Sur', 'Centro', 'Oriente'],
            'estados' => [
                'available' => 'Disponible',
                'busy' => 'Ocupado',
                'maintenance' => 'Mantenimiento'
            ]
        ];
    }

    // ==========================================
    // MONITOREO - NUEVOS MÉTODOS
    // ==========================================
    
    public static function getLiveServices(): array
    {
        return [
            [
                'id' => 1,
                'codigo' => 'DOM-2024-045',
                'tipo' => 'domicilio',
                'usuario' => 'María González',
                'conductor' => 'Jorge Ramírez',
                'vehiculo' => 'DEF-456',
                'origen' => 'Centro Comercial Cacique',
                'destino' => 'Calle 30 #15-20, Girón',
                'hora_inicio' => '10:30',
                'eta' => '11:15',
                'progreso' => 45,
                'distancia_restante' => '8.5 km',
                'status' => 'en_ruta',
                'prioridad' => 'normal'
            ],
            [
                'id' => 2,
                'codigo' => 'DOM-2024-046',
                'tipo' => 'domicilio',
                'usuario' => 'Carlos Rodríguez',
                'conductor' => 'Fernando López',
                'vehiculo' => 'MNO-345',
                'origen' => 'Megamall',
                'destino' => 'Cabecera del Llano',
                'hora_inicio' => '11:00',
                'eta' => '11:40',
                'progreso' => 65,
                'distancia_restante' => '4.2 km',
                'status' => 'en_ruta',
                'prioridad' => 'normal'
            ],
            [
                'id' => 3,
                'codigo' => 'RES-2024-001',
                'tipo' => 'reserva',
                'usuario' => 'Juan Pérez',
                'conductor' => 'Pedro Gómez',
                'vehiculo' => 'ABC-123',
                'origen' => 'Calle 45 #23-12',
                'destino' => 'Carrera 27 #34-56',
                'hora_inicio' => '08:00',
                'eta' => '12:00',
                'progreso' => 75,
                'distancia_restante' => '12 km',
                'status' => 'en_ruta',
                'prioridad' => 'alta'
            ]
        ];
    }

    public static function getVehicleStatusSummary(): array
    {
        return [
            'disponibles' => [
                'count' => 25,
                'vehiculos' => ['ABC-123', 'JKL-012', 'PQR-678']
            ],
            'en_servicio' => [
                'count' => 10,
                'vehiculos' => ['DEF-456', 'MNO-345']
            ],
            'mantenimiento' => [
                'count' => 3,
                'vehiculos' => ['GHI-789']
            ]
        ];
    }

    public static function getAlerts(): array
    {
        return [
            [
                'id' => 1,
                'tipo' => 'retraso',
                'severidad' => 'warning',
                'titulo' => 'Servicio con retraso',
                'mensaje' => 'DOM-2024-045 lleva 15 min de retraso',
                'servicio_id' => 'DOM-2024-045',
                'timestamp' => '2024-11-03 10:45',
                'leida' => false
            ],
            [
                'id' => 2,
                'tipo' => 'mantenimiento',
                'severidad' => 'info',
                'titulo' => 'Mantenimiento programado',
                'mensaje' => 'GHI-789 requiere mantenimiento en 2 días',
                'vehiculo_id' => 3,
                'timestamp' => '2024-11-03 09:30',
                'leida' => false
            ],
            [
                'id' => 3,
                'tipo' => 'combustible',
                'severidad' => 'error',
                'titulo' => 'Nivel de combustible bajo',
                'mensaje' => 'MNO-345 tiene menos del 15% de combustible',
                'vehiculo_id' => 5,
                'timestamp' => '2024-11-03 11:10',
                'leida' => false
            ]
        ];
    }

    public static function getEventTimeline(): array
    {
        return [
            [
                'id' => 1,
                'tipo' => 'servicio_iniciado',
                'icono' => 'play',
                'titulo' => 'Servicio iniciado',
                'descripcion' => 'DOM-2024-046 - Carlos Rodríguez',
                'timestamp' => '2024-11-03 11:00',
                'color' => 'blue'
            ],
            [
                'id' => 2,
                'tipo' => 'servicio_iniciado',
                'icono' => 'play',
                'titulo' => 'Servicio iniciado',
                'descripcion' => 'DOM-2024-045 - María González',
                'timestamp' => '2024-11-03 10:30',
                'color' => 'blue'
            ],
            [
                'id' => 3,
                'tipo' => 'servicio_completado',
                'icono' => 'check',
                'titulo' => 'Servicio completado',
                'descripcion' => 'RES-2024-002 - Ana Martínez',
                'timestamp' => '2024-11-03 09:30',
                'color' => 'green'
            ],
            [
                'id' => 4,
                'tipo' => 'conductor_disponible',
                'icono' => 'user',
                'titulo' => 'Conductor disponible',
                'descripcion' => 'Roberto Díaz se conectó',
                'timestamp' => '2024-11-03 08:00',
                'color' => 'gray'
            ]
        ];
    }

    // ==========================================
    // CATÁLOGO - NUEVOS MÉTODOS
    // ==========================================
    
    public static function getCatalogVehicles(array $filters = []): array
    {
        $vehicles = [
            [
                'id' => 1,
                'nombre' => 'Toyota Prado VX',
                'placa' => 'ABC-123',
                'tipo' => 'SUV',
                'marca' => 'Toyota',
                'modelo' => 'Prado',
                'year' => 2022,
                'capacidad' => 7,
                'precio_hora' => 25000,
                'precio_dia' => 180000,
                'caracteristicas' => ['Aire Acondicionado', 'GPS', 'Bluetooth', 'Cámara Reversa', 'Asientos de Cuero'],
                'imagenes' => [
                    'https://via.placeholder.com/800x600/3b82f6/ffffff?text=Toyota+Prado+1',
                    'https://via.placeholder.com/800x600/3b82f6/ffffff?text=Toyota+Prado+2',
                    'https://via.placeholder.com/800x600/3b82f6/ffffff?text=Toyota+Prado+3'
                ],
                'disponible' => true,
                'calificacion' => 4.8,
                'num_reviews' => 156,
                'sede' => 'Norte'
            ],
            [
                'id' => 2,
                'nombre' => 'Chevrolet Spark GT',
                'placa' => 'DEF-456',
                'tipo' => 'Sedan',
                'marca' => 'Chevrolet',
                'modelo' => 'Spark',
                'year' => 2023,
                'capacidad' => 5,
                'precio_hora' => 15000,
                'precio_dia' => 100000,
                'caracteristicas' => ['Aire Acondicionado', 'Bluetooth', 'Control Crucero'],
                'imagenes' => [
                    'https://via.placeholder.com/800x600/10b981/ffffff?text=Chevrolet+Spark+1',
                    'https://via.placeholder.com/800x600/10b981/ffffff?text=Chevrolet+Spark+2'
                ],
                'disponible' => false,
                'calificacion' => 4.6,
                'num_reviews' => 203,
                'sede' => 'Sur'
            ],
            [
                'id' => 3,
                'nombre' => 'Mazda CX-5 Grand Touring',
                'placa' => 'GHI-789',
                'tipo' => 'SUV',
                'marca' => 'Mazda',
                'modelo' => 'CX-5',
                'year' => 2021,
                'capacidad' => 5,
                'precio_hora' => 22000,
                'precio_dia' => 160000,
                'caracteristicas' => ['Aire Acondicionado', 'GPS', 'Bluetooth', 'Techo Solar', 'Pantalla Touch'],
                'imagenes' => [
                    'https://via.placeholder.com/800x600/f59e0b/ffffff?text=Mazda+CX5+1'
                ],
                'disponible' => false,
                'calificacion' => 4.5,
                'num_reviews' => 89,
                'sede' => 'Centro'
            ],
            [
                'id' => 4,
                'nombre' => 'Renault Duster Intens',
                'placa' => 'JKL-012',
                'tipo' => 'SUV',
                'marca' => 'Renault',
                'modelo' => 'Duster',
                'year' => 2022,
                'capacidad' => 5,
                'precio_hora' => 20000,
                'precio_dia' => 140000,
                'caracteristicas' => ['Aire Acondicionado', 'Bluetooth', '4x4', 'Barra de Techo'],
                'imagenes' => [
                    'https://via.placeholder.com/800x600/ef4444/ffffff?text=Renault+Duster+1',
                    'https://via.placeholder.com/800x600/ef4444/ffffff?text=Renault+Duster+2'
                ],
                'disponible' => true,
                'calificacion' => 4.7,
                'num_reviews' => 178,
                'sede' => 'Oriente'
            ],
            [
                'id' => 5,
                'nombre' => 'Nissan Qashqai Exclusive',
                'placa' => 'MNO-345',
                'tipo' => 'SUV',
                'marca' => 'Nissan',
                'modelo' => 'Qashqai',
                'year' => 2023,
                'capacidad' => 5,
                'precio_hora' => 23000,
                'precio_dia' => 165000,
                'caracteristicas' => ['Aire Acondicionado', 'GPS', 'Bluetooth', 'Cámara 360°', 'Sensores'],
                'imagenes' => [
                    'https://via.placeholder.com/800x600/8b5cf6/ffffff?text=Nissan+Qashqai+1'
                ],
                'disponible' => false,
                'calificacion' => 4.6,
                'num_reviews' => 134,
                'sede' => 'Norte'
            ]
        ];

        // Aplicar filtros
        if (!empty($filters['tipo'])) {
            $vehicles = array_filter($vehicles, fn($v) => $v['tipo'] === $filters['tipo']);
        }

        if (!empty($filters['disponible'])) {
            $vehicles = array_filter($vehicles, fn($v) => $v['disponible'] === true);
        }

        if (!empty($filters['capacidad'])) {
            $vehicles = array_filter($vehicles, fn($v) => $v['capacidad'] >= $filters['capacidad']);
        }

        if (!empty($filters['precio_max'])) {
            $vehicles = array_filter($vehicles, fn($v) => $v['precio_dia'] <= $filters['precio_max']);
        }

        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $vehicles = array_filter($vehicles, function($v) use ($search) {
                return str_contains(strtolower($v['nombre']), $search) ||
                       str_contains(strtolower($v['marca']), $search) ||
                       str_contains(strtolower($v['modelo']), $search);
            });
        }

        return array_values($vehicles);
    }

    public static function getVehicleById(int $id): ?array
    {
        $vehicles = self::getCatalogVehicles();
        $filtered = array_filter($vehicles, fn($v) => $v['id'] === $id);
        return !empty($filtered) ? reset($filtered) : null;
    }

    public static function getVehicleAvailability(int $vehicleId, string $fecha): array
    {
        // Simula disponibilidad horaria
        return [
            'fecha' => $fecha,
            'slots' => [
                ['hora' => '08:00', 'disponible' => true],
                ['hora' => '09:00', 'disponible' => true],
                ['hora' => '10:00', 'disponible' => false],
                ['hora' => '11:00', 'disponible' => false],
                ['hora' => '12:00', 'disponible' => true],
                ['hora' => '13:00', 'disponible' => true],
                ['hora' => '14:00', 'disponible' => true],
                ['hora' => '15:00', 'disponible' => true],
                ['hora' => '16:00', 'disponible' => false],
                ['hora' => '17:00', 'disponible' => true],
                ['hora' => '18:00', 'disponible' => true],
            ]
        ];
    }

    public static function getUserReservations(int $userId): array
    {
        return [
            [
                'id' => 1,
                'codigo' => 'RES-2024-001',
                'vehiculo' => 'Toyota Prado VX',
                'vehiculo_imagen' => 'https://via.placeholder.com/200x150/3b82f6/ffffff?text=Prado',
                'fecha_inicio' => '2024-11-05 08:00',
                'fecha_fin' => '2024-11-05 12:00',
                'origen' => 'Calle 45 #23-12',
                'destino' => 'Carrera 27 #34-56',
                'monto' => 80000,
                'status' => 'confirmada',
                'puede_cancelar' => true
            ],
            [
                'id' => 2,
                'codigo' => 'RES-2024-012',
                'vehiculo' => 'Renault Duster Intens',
                'vehiculo_imagen' => 'https://via.placeholder.com/200x150/ef4444/ffffff?text=Duster',
                'fecha_inicio' => '2024-10-28 14:00',
                'fecha_fin' => '2024-10-28 18:00',
                'origen' => 'Aeropuerto Palonegro',
                'destino' => 'Hotel Chicamocha',
                'monto' => 95000,
                'status' => 'completada',
                'puede_cancelar' => false
            ]
        ];
    }

    // ==========================================
    // REPORTES - NUEVOS MÉTODOS
    // ==========================================
    
    public static function getRevenueReport(string $periodo = 'mes'): array
    {
        return [
            'total' => 4200000,
            'cambio_porcentual' => 12,
            'por_tipo' => [
                ['tipo' => 'Reservas', 'monto' => 2800000, 'porcentaje' => 67],
                ['tipo' => 'Domicilios', 'monto' => 1400000, 'porcentaje' => 33]
            ],
            'por_dia' => [
                ['fecha' => '2024-11-01', 'monto' => 145000],
                ['fecha' => '2024-11-02', 'monto' => 178000],
                ['fecha' => '2024-11-03', 'monto' => 156000]
            ]
        ];
    }

    public static function getVehicleUsageReport(): array
    {
        return [
            [
                'vehiculo' => 'ABC-123 - Toyota Prado',
                'servicios' => 45,
                'horas_uso' => 180,
                'ingresos' => 850000,
                'tasa_ocupacion' => 75
            ],
            [
                'vehiculo' => 'DEF-456 - Chevrolet Spark',
                'servicios' => 67,
                'horas_uso' => 210,
                'ingresos' => 680000,
                'tasa_ocupacion' => 88
            ]
        ];
    }

    public static function getDriverPerformance(): array
    {
        return [
            [
                'conductor' => 'Pedro Gómez',
                'servicios_completados' => 156,
                'calificacion_promedio' => 4.8,
                'horas_trabajo' => 180,
                'ingresos_generados' => 1200000
            ],
            [
                'conductor' => 'Jorge Ramírez',
                'servicios_completados' => 203,
                'calificacion_promedio' => 4.9,
                'horas_trabajo' => 220,
                'ingresos_generados' => 1450000
            ]
        ];
    }

    // ==========================================
    // USUARIOS (Existentes)
    // ==========================================
    
    public static function getUserStats(): array
    {
        return [
            'total' => 156,
            'activos' => 142,
            'nuevos_mes' => 23,
            'inactivos' => 14
        ];
    }

    public static function getUsers(array $filters = []): array
    {
        $users = [
            [
                'id' => 1,
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@example.com',
                'phone' => '+57 300 123 4567',
                'status' => 'active',
                'reservas_count' => 12,
                'ultima_reserva' => '2024-10-28',
                'registered_at' => '2024-01-15',
                'sede' => 'Norte'
            ],
            [
                'id' => 2,
                'name' => 'María González',
                'email' => 'maria.gonzalez@example.com',
                'phone' => '+57 301 234 5678',
                'status' => 'active',
                'reservas_count' => 8,
                'ultima_reserva' => '2024-10-29',
                'registered_at' => '2024-02-20',
                'sede' => 'Sur'
            ],
            [
                'id' => 3,
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@example.com',
                'phone' => '+57 302 345 6789',
                'status' => 'inactive',
                'reservas_count' => 5,
                'ultima_reserva' => '2024-09-15',
                'registered_at' => '2024-03-10',
                'sede' => 'Centro'
            ],
            [
                'id' => 4,
                'name' => 'Ana Martínez',
                'email' => 'ana.martinez@example.com',
                'phone' => '+57 303 456 7890',
                'status' => 'active',
                'reservas_count' => 15,
                'ultima_reserva' => '2024-10-30',
                'registered_at' => '2023-12-05',
                'sede' => 'Norte'
            ],
            [
                'id' => 5,
                'name' => 'Luis Sánchez',
                'email' => 'luis.sanchez@example.com',
                'phone' => '+57 304 567 8901',
                'status' => 'active',
                'reservas_count' => 20,
                'ultima_reserva' => '2024-10-29',
                'registered_at' => '2023-11-20',
                'sede' => 'Oriente'
            ],
        ];

        if (!empty($filters['status'])) {
            $users = array_filter($users, fn($u) => $u['status'] === $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $users = array_filter($users, function($u) use ($search) {
                return str_contains(strtolower($u['name']), $search) ||
                       str_contains(strtolower($u['email']), $search);
            });
        }

        return array_values($users);
    }

    // ==========================================
    // CONDUCTORES (Existentes)
    // ==========================================
    
    public static function getDriverStats(): array
    {
        return [
            'total' => 45,
            'disponibles' => 32,
            'en_servicio' => 13,
            'inactivos' => 8
        ];
    }

    public static function getDrivers(array $filters = []): array
    {
        $drivers = [
            [
                'id' => 1,
                'name' => 'Pedro Gómez',
                'email' => 'pedro.gomez@bgago.com',
                'phone' => '+57 310 111 2222',
                'license' => 'C2-12345678',
                'status' => 'available',
                'vehiculo_asignado' => 'ABC-123',
                'servicios_completados' => 156,
                'calificacion' => 4.8,
                'ultima_actividad' => '2024-10-30 08:30',
                'sede' => 'Norte'
            ],
            [
                'id' => 2,
                'name' => 'Jorge Ramírez',
                'email' => 'jorge.ramirez@bgago.com',
                'phone' => '+57 311 222 3333',
                'license' => 'C2-23456789',
                'status' => 'busy',
                'vehiculo_asignado' => 'DEF-456',
                'servicios_completados' => 203,
                'calificacion' => 4.9,
                'ultima_actividad' => '2024-10-30 10:15',
                'sede' => 'Sur'
            ],
            [
                'id' => 3,
                'name' => 'Miguel Torres',
                'email' => 'miguel.torres@bgago.com',
                'phone' => '+57 312 333 4444',
                'license' => 'C2-34567890',
                'status' => 'inactive',
                'vehiculo_asignado' => 'GHI-789',
                'servicios_completados' => 89,
                'calificacion' => 4.5,
                'ultima_actividad' => '2024-10-25 16:45',
                'sede' => 'Centro'
            ],
            [
                'id' => 4,
                'name' => 'Roberto Díaz',
                'email' => 'roberto.diaz@bgago.com',
                'phone' => '+57 313 444 5555',
                'license' => 'C2-45678901',
                'status' => 'available',
                'vehiculo_asignado' => 'JKL-012',
                'servicios_completados' => 178,
                'calificacion' => 4.7,
                'ultima_actividad' => '2024-10-30 07:00',
                'sede' => 'Oriente'
            ],
            [
                'id' => 5,
                'name' => 'Fernando López',
                'email' => 'fernando.lopez@bgago.com',
                'phone' => '+57 314 555 6666',
                'license' => 'C2-56789012',
                'status' => 'busy',
                'vehiculo_asignado' => 'MNO-345',
                'servicios_completados' => 134,
                'calificacion' => 4.6,
                'ultima_actividad' => '2024-10-30 09:20',
                'sede' => 'Norte'
            ],
        ];

        if (!empty($filters['status'])) {
            $drivers = array_filter($drivers, fn($d) => $d['status'] === $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $drivers = array_filter($drivers, function($d) use ($search) {
                return str_contains(strtolower($d['name']), $search) ||
                       str_contains(strtolower($d['license']), $search);
            });
        }

        return array_values($drivers);
    }

    // ==========================================
    // VEHÍCULOS (Existentes)
    // ==========================================
    
    public static function getVehicleStats(): array
    {
        return [
            'total' => 38,
            'disponibles' => 25,
            'en_servicio' => 10,
            'mantenimiento' => 3
        ];
    }

    public static function getVehicles(array $filters = []): array
    {
        $vehicles = [
            [
                'id' => 1,
                'placa' => 'ABC-123',
                'marca' => 'Toyota',
                'modelo' => 'Prado',
                'year' => 2022,
                'tipo' => 'SUV',
                'capacidad' => 7,
                'status' => 'available',
                'conductor_asignado' => 'Pedro Gómez',
                'kilometraje' => 45000,
                'ultimo_mantenimiento' => '2024-10-15',
                'proximo_mantenimiento' => '2024-11-15',
                'sede' => 'Norte'
            ],
            [
                'id' => 2,
                'placa' => 'DEF-456',
                'marca' => 'Chevrolet',
                'modelo' => 'Spark',
                'year' => 2023,
                'tipo' => 'Sedan',
                'capacidad' => 5,
                'status' => 'busy',
                'conductor_asignado' => 'Jorge Ramírez',
                'kilometraje' => 22000,
                'ultimo_mantenimiento' => '2024-10-20',
                'proximo_mantenimiento' => '2024-11-20',
                'sede' => 'Sur'
            ],
            [
                'id' => 3,
                'placa' => 'GHI-789',
                'marca' => 'Mazda',
                'modelo' => 'CX-5',
                'year' => 2021,
                'tipo' => 'SUV',
                'capacidad' => 5,
                'status' => 'maintenance',
                'conductor_asignado' => null,
                'kilometraje' => 67000,
                'ultimo_mantenimiento' => '2024-10-28',
                'proximo_mantenimiento' => '2024-12-01',
                'sede' => 'Centro'
            ],
            [
                'id' => 4,
                'placa' => 'JKL-012',
                'marca' => 'Renault',
                'modelo' => 'Duster',
                'year' => 2022,
                'tipo' => 'SUV',
                'capacidad' => 5,
                'status' => 'available',
                'conductor_asignado' => 'Roberto Díaz',
                'kilometraje' => 38000,
                'ultimo_mantenimiento' => '2024-10-10',
                'proximo_mantenimiento' => '2024-11-10',
                'sede' => 'Oriente'
            ],
            [
                'id' => 5,
                'placa' => 'MNO-345',
                'marca' => 'Nissan',
                'modelo' => 'Qashqai',
                'year' => 2023,
                'tipo' => 'SUV',
                'capacidad' => 5,
                'status' => 'busy',
                'conductor_asignado' => 'Fernando López',
                'kilometraje' => 15000,
                'ultimo_mantenimiento' => '2024-10-25',
                'proximo_mantenimiento' => '2024-11-25',
                'sede' => 'Norte'
            ],
        ];

        if (!empty($filters['status'])) {
            $vehicles = array_filter($vehicles, fn($v) => $v['status'] === $filters['status']);
        }

        if (!empty($filters['tipo'])) {
            $vehicles = array_filter($vehicles, fn($v) => $v['tipo'] === $filters['tipo']);
        }

        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $vehicles = array_filter($vehicles, function($v) use ($search) {
                return str_contains(strtolower($v['placa']), $search) ||
                       str_contains(strtolower($v['marca']), $search) ||
                       str_contains(strtolower($v['modelo']), $search);
            });
        }

        return array_values($vehicles);
    }

    // ==========================================
    // RESERVAS (Existentes)
    // ==========================================
    
    public static function getReservationStats(): array
    {
        return [
            'pendientes' => 12,
            'activas' => 24,
            'completadas_hoy' => 18,
            'canceladas_mes' => 5
        ];
    }

    public static function getReservations(array $filters = []): array
    {
        $reservations = [
            [
                'id' => 1,
                'codigo' => 'RES-2024-001',
                'usuario' => 'Juan Pérez',
                'conductor' => 'Pedro Gómez',
                'vehiculo' => 'ABC-123 - Toyota Prado',
                'origen' => 'Calle 45 #23-12, Bucaramanga',
                'destino' => 'Carrera 27 #34-56, Floridablanca',
                'fecha_inicio' => '2024-10-30 08:00',
                'fecha_fin' => '2024-10-30 12:00',
                'tipo' => 'reserva',
                'status' => 'active',
                'monto' => 80000,
                'sede' => 'Norte'
            ],
            [
                'id' => 2,
                'codigo' => 'DOM-2024-045',
                'usuario' => 'María González',
                'conductor' => 'Jorge Ramírez',
                'vehiculo' => 'DEF-456 - Chevrolet Spark',
                'origen' => 'Centro Comercial Cacique',
                'destino' => 'Calle 30 #15-20, Girón',
                'fecha_inicio' => '2024-10-30 10:30',
                'fecha_fin' => '2024-10-30 11:30',
                'tipo' => 'domicilio',
                'status' => 'active',
                'monto' => 25000,
                'sede' => 'Sur'
            ],
            [
                'id' => 3,
                'codigo' => 'RES-2024-002',
                'usuario' => 'Ana Martínez',
                'conductor' => 'Roberto Díaz',
                'vehiculo' => 'JKL-012 - Renault Duster',
                'origen' => 'Aeropuerto Palonegro',
                'destino' => 'Hotel Chicamocha',
                'fecha_inicio' => '2024-10-29 14:00',
                'fecha_fin' => '2024-10-29 15:30',
                'tipo' => 'reserva',
                'status' => 'completed',
                'monto' => 120000,
                'sede' => 'Norte'
            ],
            [
                'id' => 4,
                'codigo' => 'RES-2024-003',
                'usuario' => 'Luis Sánchez',
                'conductor' => null,
                'vehiculo' => null,
                'origen' => 'Universidad Industrial de Santander',
                'destino' => 'Parque del Agua',
                'fecha_inicio' => '2024-10-31 09:00',
                'fecha_fin' => '2024-10-31 13:00',
                'tipo' => 'reserva',
                'status' => 'pending',
                'monto' => 95000,
                'sede' => 'Centro'
            ],
            [
                'id' => 5,
                'codigo' => 'DOM-2024-046',
                'usuario' => 'Carlos Rodríguez',
                'conductor' => 'Fernando López',
                'vehiculo' => 'MNO-345 - Nissan Qashqai',
                'origen' => 'Megamall',
                'destino' => 'Cabecera del Llano',
                'fecha_inicio' => '2024-10-30 11:00',
                'fecha_fin' => '2024-10-30 12:00',
                'tipo' => 'domicilio',
                'status' => 'active',
                'monto' => 30000,
                'sede' => 'Oriente'
            ],
        ];

        if (!empty($filters['status'])) {
            $reservations = array_filter($reservations, fn($r) => $r['status'] === $filters['status']);
        }

        if (!empty($filters['tipo'])) {
            $reservations = array_filter($reservations, fn($r) => $r['tipo'] === $filters['tipo']);
        }

        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $reservations = array_filter($reservations, function($r) use ($search) {
                return str_contains(strtolower($r['codigo']), $search) ||
                       str_contains(strtolower($r['usuario']), $search);
            });
        }

        return array_values($reservations);
    }

    // ==========================================
    // HELPERS
    // ==========================================
    
    public static function getStatusLabel(string $status): string
    {
        $labels = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'available' => 'Disponible',
            'busy' => 'Ocupado',
            'maintenance' => 'Mantenimiento',
            'pending' => 'Pendiente',
            'completed' => 'Completado',
            'cancelled' => 'Cancelado',
            'en_ruta' => 'En Ruta',
            'confirmada' => 'Confirmada'
        ];

        return $labels[$status] ?? ucfirst($status);
    }

    public static function getStatusColor(string $status): string
    {
        $colors = [
            'active' => 'green',
            'inactive' => 'red',
            'available' => 'green',
            'busy' => 'yellow',
            'maintenance' => 'orange',
            'pending' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            'en_ruta' => 'blue',
            'confirmada' => 'green'
        ];

        return $colors[$status] ?? 'gray';
    }

    public static function getSeverityColor(string $severity): string
    {
        $colors = [
            'info' => 'blue',
            'warning' => 'yellow',
            'error' => 'red',
            'success' => 'green'
        ];

        return $colors[$severity] ?? 'gray';
    }

    // ==========================================
    // DASHBOARD - MÉTODOS COMPATIBLES CON FACADE
    // ==========================================

    /**
     * Alias: getReservasChart()
     * Devuelve los datos mensuales de reservas para el gráfico principal
     */
    public static function getReservasChart(): array
    {
        return self::getReservasPorMes();
    }

    /**
     * Alias: getDistribucionChart()
     * Devuelve los datos de distribución de sedes para gráfico de torta
     */
    public static function getDistribucionChart(): array
    {
        $data = self::getDistribucionSedes();

        // Añadimos colores para los gráficos
        $data['colors'] = [
            'rgba(59, 130, 246, 0.8)', // azul
            'rgba(16, 185, 129, 0.8)', // verde
            'rgba(245, 158, 11, 0.8)', // naranja
            'rgba(239, 68, 68, 0.8)'   // rojo
        ];

        return $data;
    }

    /**
     * Alias: getDashboardStats()
     * (ya existe, solo se deja para compatibilidad con la interfaz Data)
     */
}