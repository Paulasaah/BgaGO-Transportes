<?php

namespace App\Services;

/**
 * Servicio para proveer datos estáticos mientras no hay base de datos
 * 
 * IMPORTANTE: Una vez tengas las migraciones y modelos:
 * 1. Cambia los métodos para usar Eloquent
 * 2. Ejemplo: return User::where('status', 'active')->count();
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
    // USUARIOS
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

        // Aplicar filtros si existen
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
    // CONDUCTORES
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

        // Aplicar filtros
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
    // VEHÍCULOS
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

        // Aplicar filtros
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
    // RESERVAS
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

        // Aplicar filtros
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
            'cancelled' => 'Cancelado'
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
            'cancelled' => 'red'
        ];

        return $colors[$status] ?? 'gray';
    }
}