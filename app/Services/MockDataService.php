<?php

namespace App\Services;

/**
 * Servicio para proveer datos estáticos mientras no hay base de datos
 * 
 * IMPORTANTE: Una vez tengas las migraciones y modelos:
 * 1. Renombra este archivo a UserService.php, DriverService.php, etc.
 * 2. Reemplaza los arrays por queries de Eloquent
 * 3. Mantén las mismas firmas de métodos (mismo nombre, mismo return type)
 */
class MockDataService
{
    /**
     * ============================================
     * USUARIOS
     * ============================================
     */

    /**
     * Obtener estadísticas de usuarios
     */
    public static function getUserStats(): array
    {
        return [
            'total' => 1247,
            'active_today' => 328,
            'new_users' => 42,
            'suspended' => 8,
        ];
    }

    /**
     * Obtener lista de usuarios
     */
    public static function getUsers(): array
    {
        return [
            (object) [
                'id' => 1001,
                'name' => 'Juan Delgado',
                'initials' => 'JD',
                'email' => 'juan.delgado@email.com',
                'phone' => '+57 301 234 5678',
                'sede' => 'Cabecera',
                'status' => 'active',
                'status_label' => 'Activo',
                'registered_at' => '15 Mar 2024',
                'reservations_count' => 23,
                'last_reservation' => 'Hace 2 días',
            ],
            (object) [
                'id' => 1002,
                'name' => 'María Rodríguez',
                'initials' => 'MR',
                'email' => 'maria.r@email.com',
                'phone' => '+57 312 567 8901',
                'sede' => 'Cañaveral',
                'status' => 'active',
                'status_label' => 'Activo',
                'registered_at' => '22 Mar 2024',
                'reservations_count' => 8,
                'last_reservation' => 'Hoy',
            ],
            (object) [
                'id' => 1003,
                'name' => 'Carlos Gómez',
                'initials' => 'CG',
                'email' => 'c.gomez@email.com',
                'phone' => '+57 320 789 0123',
                'sede' => 'Piedecuesta',
                'status' => 'suspended',
                'status_label' => 'Suspendido',
                'registered_at' => '10 Feb 2024',
                'reservations_count' => 45,
                'last_reservation' => 'Hace 15 días',
            ],
            (object) [
                'id' => 1004,
                'name' => 'Ana Martínez',
                'initials' => 'AM',
                'email' => 'ana.m@email.com',
                'phone' => '+57 315 432 1098',
                'sede' => 'Florida',
                'status' => 'active',
                'status_label' => 'Activo',
                'registered_at' => '05 Abr 2024',
                'reservations_count' => 15,
                'last_reservation' => 'Hace 1 semana',
            ],
            (object) [
                'id' => 1005,
                'name' => 'Pedro Sánchez',
                'initials' => 'PS',
                'email' => 'pedro.s@email.com',
                'phone' => '+57 304 876 5432',
                'sede' => 'Cabecera',
                'status' => 'inactive',
                'status_label' => 'Inactivo',
                'registered_at' => '28 Ene 2024',
                'reservations_count' => 3,
                'last_reservation' => 'Hace 2 meses',
            ],
            (object) [
                'id' => 1006,
                'name' => 'Laura González',
                'initials' => 'LG',
                'email' => 'laura.g@email.com',
                'phone' => '+57 311 345 6789',
                'sede' => 'Cañaveral',
                'status' => 'active',
                'status_label' => 'Activo',
                'registered_at' => '12 Mar 2024',
                'reservations_count' => 31,
                'last_reservation' => 'Hace 5 horas',
            ],
            (object) [
                'id' => 1007,
                'name' => 'Roberto Díaz',
                'initials' => 'RD',
                'email' => 'roberto.d@email.com',
                'phone' => '+57 305 987 6543',
                'sede' => 'Piedecuesta',
                'status' => 'active',
                'status_label' => 'Activo',
                'registered_at' => '08 Abr 2024',
                'reservations_count' => 12,
                'last_reservation' => 'Ayer',
            ],
            (object) [
                'id' => 1008,
                'name' => 'Sofía Torres',
                'initials' => 'ST',
                'email' => 'sofia.t@email.com',
                'phone' => '+57 319 234 5678',
                'sede' => 'Florida',
                'status' => 'active',
                'status_label' => 'Activo',
                'registered_at' => '20 Feb 2024',
                'reservations_count' => 28,
                'last_reservation' => 'Hace 3 días',
            ],
        ];
    }

    /**
     * ============================================
     * CONDUCTORES
     * ============================================
     */

    /**
     * Obtener estadísticas de conductores
     */
    public static function getDriverStats(): array
    {
        return [
            'total' => 86,
            'active' => 32,
            'available' => 41,
            'offline' => 13,
        ];
    }

    /**
     * Obtener lista de conductores
     */
    public static function getDrivers(): array
    {
        return [
            (object) [
                'id' => 2001,
                'name' => 'Andrés Moreno',
                'initials' => 'AM',
                'code' => 'CON-2024-001',
                'email' => 'andres.m@bgago.com',
                'phone' => '+57 300 111 2222',
                'sede' => 'Cabecera',
                'status' => 'active',
                'status_label' => 'En Servicio',
                'vehicle_type' => 'Moto Honda',
                'vehicle_plate' => 'ABC-123',
                'deliveries_today' => 12,
                'active_deliveries' => 2,
                'rating' => 4.8,
                'total_ratings' => 245,
                'last_activity' => 'Hace 5 min',
            ],
            (object) [
                'id' => 2002,
                'name' => 'Laura Pérez',
                'initials' => 'LP',
                'code' => 'CON-2024-015',
                'email' => 'laura.p@bgago.com',
                'phone' => '+57 301 222 3333',
                'sede' => 'Cañaveral',
                'status' => 'available',
                'status_label' => 'Disponible',
                'vehicle_type' => 'Bici Eléctrica',
                'vehicle_plate' => 'BGA-045',
                'deliveries_today' => 8,
                'active_deliveries' => 0,
                'rating' => 4.9,
                'total_ratings' => 189,
                'last_activity' => 'Hace 12 min',
            ],
            (object) [
                'id' => 2003,
                'name' => 'Roberto Silva',
                'initials' => 'RS',
                'code' => 'CON-2024-008',
                'email' => 'roberto.s@bgago.com',
                'phone' => '+57 302 333 4444',
                'sede' => 'Florida',
                'status' => 'offline',
                'status_label' => 'Fuera de Servicio',
                'vehicle_type' => null,
                'vehicle_plate' => null,
                'deliveries_today' => 0,
                'active_deliveries' => 0,
                'rating' => 4.7,
                'total_ratings' => 312,
                'last_activity' => 'Hace 3 horas',
            ],
            (object) [
                'id' => 2004,
                'name' => 'Diana Castro',
                'initials' => 'DC',
                'code' => 'CON-2024-022',
                'email' => 'diana.c@bgago.com',
                'phone' => '+57 303 444 5555',
                'sede' => 'Piedecuesta',
                'status' => 'active',
                'status_label' => 'En Servicio',
                'vehicle_type' => 'Moto Yamaha',
                'vehicle_plate' => 'XYZ-789',
                'deliveries_today' => 15,
                'active_deliveries' => 3,
                'rating' => 4.9,
                'total_ratings' => 423,
                'last_activity' => 'Hace 2 min',
            ],
            (object) [
                'id' => 2005,
                'name' => 'Miguel Ángel Ruiz',
                'initials' => 'MR',
                'code' => 'CON-2024-033',
                'email' => 'miguel.r@bgago.com',
                'phone' => '+57 304 555 6666',
                'sede' => 'Cabecera',
                'status' => 'available',
                'status_label' => 'Disponible',
                'vehicle_type' => 'Patineta Eléctrica',
                'vehicle_plate' => 'BGA-078',
                'deliveries_today' => 5,
                'active_deliveries' => 0,
                'rating' => 4.6,
                'total_ratings' => 156,
                'last_activity' => 'Hace 8 min',
            ],
            (object) [
                'id' => 2006,
                'name' => 'Camila Vargas',
                'initials' => 'CV',
                'code' => 'CON-2024-041',
                'email' => 'camila.v@bgago.com',
                'phone' => '+57 305 666 7777',
                'sede' => 'Cañaveral',
                'status' => 'active',
                'status_label' => 'En Servicio',
                'vehicle_type' => 'Bici Manual',
                'vehicle_plate' => 'BGA-092',
                'deliveries_today' => 10,
                'active_deliveries' => 1,
                'rating' => 4.8,
                'total_ratings' => 201,
                'last_activity' => 'Hace 1 min',
            ],
        ];
    }

    /**
     * ============================================
     * DASHBOARD
     * ============================================
     */

    /**
     * Obtener estadísticas del dashboard principal
     */
    public static function getDashboardStats(): array
    {
        return [
            'active_reservations' => [
                'value' => 24,
                'change' => '+5 desde ayer',
                'change_type' => 'positive',
            ],
            'deliveries_today' => [
                'value' => 18,
                'change' => '+3 en progreso',
                'change_type' => 'positive',
            ],
            'monthly_revenue' => [
                'value' => '$4.2M',
                'change' => '+12% vs mes anterior',
                'change_type' => 'positive',
            ],
            'maintenance' => [
                'value' => 5,
                'change' => '2 requieren atención',
                'change_type' => 'neutral',
            ],
        ];
    }

    /**
     * Obtener datos para gráfico de reservas por mes
     */
    public static function getReservationsChartData(): array
    {
        return [
            'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            'data' => [65, 78, 90, 81, 95, 103, 110, 98, 115, 122, 130, 140],
        ];
    }

    /**
     * Obtener datos para gráfico de distribución por sede
     */
    public static function getDistributionChartData(): array
    {
        return [
            'labels' => ['Sede Cabecera', 'Sede Cañaveral', 'Sede Piedecuesta', 'Sede Florida'],
            'data' => [35, 28, 22, 15],
        ];
    }
}