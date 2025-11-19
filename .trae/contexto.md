# Contexto del Proyecto BgaGO-Transportes

## Resumen Ejecutivo
- Qué: Plataforma web para gestión de transporte y logística urbana en el AMB.
- Cómo: Laravel MVC + Livewire + Tailwind + MySQL + MQTT + Azure + MercadoPago.
- Para qué: Movilidad sostenible, optimización de flotas, domicilios, herramientas administrativas.
- Alcance: Autenticación multi‑rol, reservas en tiempo real, domicilios, rastreo GPS, pagos, notificaciones, panel admin, PWA, testing + CI/CD, monitoreo + backups.

## Stack y Arquitectura
- Backend: `Laravel 12` con estructura MVC y servicios en `app/Services`.
- UI: Blade + Livewire + Alpine; componentes Flux UI en `resources/views/components`.
- Datos: MySQL; migraciones en `database/migrations`; seeders en `database/seeders`.
- Tiempo real: MQTT (`iot/`), Leaflet para mapas, Azure APIs (`app/Http/Controllers/Api`).
- Pagos: MercadoPago (webhooks en `app/Http/Controllers/Api/PaymentController.php`).
- Autenticación/Roles: Spatie Permission; migración `create_permission_tables` y seeder `RolePermissionSeeder`.

## Estructura Principal del Código
- `app/Http/Controllers/Admin/*`: Panel administrativo (usuarios, vehículos, reservas, dashboard).
- `app/Http/Controllers/Api/*`: Endpoints de IoT/telemetría/Azure/pagos/reservas.
- `app/Livewire/*`: Componentes interactivos (mapa, reportes, dashboard simple, monitoreo).
- `app/Services/*`: Lógica de negocio (reservas, disponibilidad, precios, datos demo/mock).
- `app/Models/*`: Entidades (User, Vehicle, Reservation, Delivery, Payment, GpsTrack, etc.).
- `resources/views/*`: Vistas Blade para admin, componentes, layouts, dashboard conductor.
- `routes/web.php`: Rutas web; incluye dashboard admin y rutas del conductor.
- `config/*`: Config del framework, colas, permisos, servicios, MQTT, etc.
- `database/seeders/*`: Datos iniciales (usuarios, roles, vehículos, reservas, pagos, etc.).

## Roles y Permisos
- Roles esperados: `SuperAdmin`, `Admin`, `Conductor`, `Usuario`.
- Permisos granulares definidos por seeder de roles; middleware `auth`, `role`, `permission` en rutas protegidas.

## Módulos Funcionales
- Reservas: Estados `pending/confirmed/active/completed/cancelled`; servicios en `ReservationService.php`.
- Domicilios: Estados `pending/assigned/in_transit/delivered/cancelled`; API y Admin.
- Flotas: CRUD de vehículos, asignación a conductores; mantenimiento y documentos.
- GPS/Mapa: `Livewire\Map\MapView` con Leaflet, auto‑refresh, clustering según reglas.
- Pagos: Preferencias, webhook, actualización de `Reservation` y `Payment`.
- Reportes: `Livewire\Reports/*` (ingresos, performance, uso de vehículos).
- Monitoreo: Estado de servicios, eventos, alertas (`Livewire\Monitoring/*`).

## Integraciones
- MQTT Mosquitto: tópicos `vehicles/{vehicle_id}/location|status`; QoS 1, retain.
- Azure: controladores API para dispositivos/telemetría.
- MercadoPago: webhook seguro (firma, idempotencia, colas). 

## Datos y Seeders
- Seeders clave: `UserSeeder`, `VehicleSeeder`, `ReservationSeeder`, `PaymentSeeder`, `DeliverySeeder`, `BranchSeeder`, `DriverProfileSeeder`, `RolePermissionSeeder`, `EventSeeder`, `TransactionLogSeeder`, `PaymentMethodSeeder`, `ConfiguracionSeeder`, `VehicleMaintenanceSeeder`.
- Uso en UI: dashboard admin y conductor consumen datos de reservas, vehículos, montos y estados.

## UI y Diseño
- Base: Flux UI + Tailwind; dark mode completo; componentes reutilizables (`components/*`).
- Mapas: Leaflet con popups personalizados, z‑index controlado, `preferCanvas` y `ResizeObserver`.
- Accesibilidad: Focus visible, contraste AA, navegable con teclado.

## Seguridad
- Autenticación web + Sanctum API; bloqueo de sesión, MFA para admins.
- Validación estricta con Form Requests; políticas (`app/Policies`) por recurso.
- Webhooks y APIs con validación de firmas, idempotencia y rate limit.

## Performance
- Eager loading obligatorio; índices en tablas críticas (reservas, vehículos, gps, transacciones).
- Cache de vistas/rutas; colas con Horizon; throttling de actualizaciones en mapa.

## Testing y CI/CD
- Testing unit/feature/Dusk planificado; pipelines en `.github/workflows/` (`tests.yml`, `lint.yml`).
- Build con Vite; despliegue Azure con pasos de migración, cache clear y health check.

## Monitoreo y Backups
- Health checks, métricas operativas, logging estructurado.
- Backups nocturnos, retención definida; restauración probada según reglas del proyecto.

## Estado Actual vs Reglas del Proyecto
- Actual: Servicios, Livewire y vistas admin/conductor implementadas; mapa y reportes activos.
- Alineación: Cumple la guía de `project_rules.md` en seguridad, UI, GPS, pagos, y panel admin.
- Pendientes: Profundizar testing automatizado, PWA completa, y documentación detallada de API/Wiki.