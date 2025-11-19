Es importante que el context.md se actualice con las reglas del proyecto y conforme se va a desarrollar, se va a ir actualizando este archivo.

Reglas Proyecto BgaGO
Stack: Laravel 12 + Livewire + Tailwind + MySQL +
MQTT + Azure + MercadoPago
1. ARQUITECTURA LARAVEL
Estructura MVC: Controllers orquestan | Services lógica negocio | Repositories queries |
Actions operaciones únicas | DTOs transferencia | Form Requests validación | Resources
responses
Organización: app/{Actions, Services, Repositories, DTOs, Enums, Traits} | Eloquent: eager
loading obligatorio, scopes queries, observers eventos, soft deletes todo, timestamps auto
Validación: Form Requests controllers | Rules compartidos Traits | Backend nunca confiar
frontend | Validar antes guardar siempre
2. AUTENTICACIÓN Y ROLES (Spatie Permission)
Roles: SuperAdmin (gestiona admins/drivers, permisos totales) | Admin (operaciones,
reportes, config) | Conductor (viajes, ubicación, estado) | Usuario (reservas, pagos, historial)
Permisos Granulares: vehicles.{view,create,edit,delete} |
reservations.{view,approve,cancel} | trips.{view,assign,complete} | payments.{view,refund} |
reports.{view,export}
Seguridad: Laravel Sanctum API + Session web | Bcrypt passwords | JWT 15min + refresh
7días | MFA SuperAdmin/Admins | Lockout 5 intentos 15min | Session timeout 2h | Throttle
60/min usuarios, 120/min conductores | IP whitelist SuperAdmin
Middleware: auth → verified → role → permission en cada ruta protegida
3. RESERVAS Y DISPONIBILIDAD
Estados: pending, confirmed, active, completed, cancelled, expired
Disponibilidad Tiempo Real: Lock pesimista DB al reservar | Transacción atómica: verificar
disponibilidad→crear reserva→bloquear vehículo | Queue liberación auto si no confirma
15min | Optimistic locking (version column) evitar double booking
Reglas Negocio: No solapamiento horarios mismo vehículo | Buffer 30min entre reservas |
Validar licencia conductor antes asignar | Verificar pago válido antes confirmar | Cancelación
gratuita >24h, penalización <24h
Índices DB: (vehicle_id, start_time, end_time) | (user_id, status, created_at) | (status,
created_at)
4. GEOLOCALIZACIÓN GPS + MQTT
Mosquitto Topics: vehicles/{vehicle_id}/location,
vehicles/{vehicle_id}/status | QoS 1 entrega garantizada | Retain last message
última ubicación | Heartbeat 30s, timeout 90s marca offline
Almacenamiento: Tabla gps_logs particionada por mes | Índice (vehicle_id, created_at) |
Retener 90 días, archivar anteriores | Agregación 5min reportes históricos
Leaflet.js: Actualizar marcadores cada 10s AJAX | WebSockets actualizaciones críticas |
Clustering >50 vehículos | Geofencing zonas | Rutas OpenStreetMap | Throttle updates
frontend 10s
Performance: Última ubicación en tabla separada (vehicle_id UNIQUE) actualización
constante | Broadcast solo usuarios suscritos vehículo específico
5. DOMICILIOS Y ASIGNACIÓN
Estados: pending, assigned, in_transit, delivered, cancelled
Asignación: Algoritmo: conductor más cercano disponible + rating + carga actual | Timeout
2min reasignar auto | Queue asignaciones (Horizon) | Notificar conductor | Puede rechazar
2x/día
Tracking: Ruta conductor→pickup→delivery | ETA calculado cada GPS update | Notificar
hitos: asignado, recogido, en camino, entregado | Prueba entrega: foto + firma +
geolocalización
Optimización: Batch destinos similares misma zona | Prioridad express vs económico
6. PAGOS MERCADOPAGO
Flujo: Crear preferencia con metadata (reservation_id, user_id) → Redirect checkout →
Webhook notificación → Verificar signature → Consultar API estado → Actualizar reserva +
transaction_id → Email confirmación
Seguridad Webhook: Verificar IP whitelist | Validar X-Signature | Idempotencia verificar
transaction_id no procesado | Queue procesamiento | Timeout 30s | Retry exponencial falla
Estados: pending, approved, rejected, refunded, cancelled | Tabla transactions: amount,
status, gateway_response, metadata, user_id, ip, user_agent | Soft deletes NUNCA |
Auditoría completa
Refunds: Manual SuperAdmin/Admin | Validar elegibilidad | Registrar motivo + aprobador |
Notificar usuario
7. NOTIFICACIONES
Email (Laravel Mail): Mailtrap dev, SMTP prod | Eventos: registro verificación, login nuevo
dispositivo, reserva (creada/confirmada/cancelada/recordatorio 24h), viaje
(asignado/iniciado/completado), pago (confirmado/fallido/refund), sistema
(mantenimiento/cambios términos)
Queue Jobs (Horizon): Retry 3 veces exponencial | Marcar fallidos después 3 intentos |
Log todos intentos | Rate limit: max 10 emails/hora usuario
Templates: Blade mailable Markdown | Personalización nombre, logo, colores |
Unsubscribe link obligatorio
Tracking: Tabla notifications polimórfica | Read/unread status | Archive después 90
días
8. BASE DE DATOS MYSQL
Diseño: Normalización 3NF | UUIDs IDs expuestos API/URLs | Soft deletes tablas
principales | Timestamps auto | Deleted_by, updated_by auditoría
Índices Críticos:
● Vehículos: (status, available_from, available_to) | (type, status)
● Reservas: (user_id, status, created_at) | (vehicle_id, start_time, end_time) | (status,
created_at)
● GPS: (vehicle_id, created_at) particionado | (created_at) cleanup
● Transacciones: (user_id, created_at) | (status, created_at) |
UNIQUE(gateway_transaction_id)
Particionamiento: gps_logs por RANGE(YEAR, MONTH) | Cleanup auto >90 días
Migrations: Solo adelante | Down implementado | Seeders datos iniciales | Factories testing
| Backup antes deployment
Backups: Diarios 3AM auto | Retention: 7 diarios, 4 semanales, 12 mensuales | Test restore
mensual
9. PANEL ADMINISTRATIVO
Dashboard: KPIs: vehículos activos, reservas hoy, ingresos mes, conductores online |
Charts.js: reservas periodo, ingresos, vehículos usados, rating conductores | Mapa tiempo
real vehículos activos | Alertas: fuera zona, bajo combustible, mantenimiento vencido
Reportes: Exportar Excel/PDF/CSV: reservas periodo, ingresos detallados, performance
conductores, uso vehículos (km/horas/frecuencia), log auditoría
Gestión: Vehículos CRUD + fotos + mantenimiento + documentos + historial + tarifas |
Usuarios/Conductores: ver/editar/suspender + historial + rating + documentos + permisos
10. LIVEWIRE + ALPINE.JS
Livewire: Componente por funcionalidad (VehicleList, ReservationForm, DriverMap) | Props
inmutables | $dispatch eventos | Polling solo necesario (mapa 10s) | Lazy loading pesados |
wire:model.debounce.500ms búsquedas | wire:loading feedback | Paginate listas largas |
Defer no críticos wire:init
Alpine.js: UI simple (dropdowns, modales, tabs) | Sin lógica negocio, solo presentación |
x-data pequeños focalizados | Complementa Livewire
Security: Validar todo método backend | Authorize cada acción | Rate limit requests | CSRF
auto
11. TAILWIND + FLUX UI
Diseño: Flux UI base + componentes propios extendidos | Tokens: colores, espaciado,
tipografía | Dark mode completo | Mobile first | Breakpoints: sm,md,lg,xl,2xl | Touch targets
44x44px mínimo
Performance: Purge CSS unused | Fonts locales | Icons inline SVG | Lazy load images
Accesibilidad: Contraste WCAG AA | Focus visible | Screen reader labels | Keyboard
navigation completa
12. PWA
Manifest: Icons 192x192, 512x512 | Start URL, scope, display standalone | Theme color |
Short name ≤12 chars
Service Worker: Workbox caching | Cache páginas principales offline | Network first datos
tiempo real | Cache first assets estáticos | Add to home prompt | Offline fallback page
13. TESTING
Unit: Models (relaciones, scopes, mutators) | Services (lógica negocio) | Repositories
(queries) | Actions (operaciones) | Coverage >80%
Feature: Auth flows | Reservas end-to-end | Pagos mocks Mercadopago | Asignación
conductores | Notificaciones
Dusk: Flujos críticos: reservar vehículo, proceso pago, panel admin
Rules: RefreshDatabase | Factories data | Arrange-Act-Assert | Descriptive names
14. CI/CD GITHUB ACTIONS
Pipeline: Checkout → Setup PHP 8.2 + Composer → Setup Node + pnpm → .env.testing →
Composer install prod → pnpm install → Generate key → Migrations → PHPUnit → PHP CS
Fixer → PHPStan → Build assets → Deploy si main y tests pass
Deploy Azure: SSH script | Maintenance ON → Git pull → Composer prod → pnpm build →
Migrations → Cache clear → Queue restart → Maintenance OFF → Health check
Rollback: Git tags cada deploy | Script auto si health check falla | Backup DB antes
migrations
15. INFRAESTRUCTURA AZURE + DOCKER
Docker Compose: app (Laravel PHP 8.2-fpm) | mysql (8.0 + backup script) | mosquitto
(MQTT 1883, 9001 WebSocket) | Volumes persistent
Apache: Virtual host | SSL Let's Encrypt | HTTP/2 | Gzip | Security headers
Monitoreo: Telescope dev | Horizon queues | Logs agregados | Uptime monitor |
Performance APM
Seguridad VM: UFW: 80, 443, SSH | SSH key-only | Fail2ban | Updates auto | Usuario
no-root app
16. SEGURIDAD
Inyecciones: Eloquent evita SQL | Blade escapa XSS | Validar Form Requests | Sanitizar
antes guardar
CSRF: Token auto forms | Verificar POST | SameSite Strict
Rate Limit: 60/min auth | 120/min usuarios | 300/min conductores | 10/min webhooks IP
Logs: Login intentos | Cambios permisos | Acciones SuperAdmin | Accesos sensibles |
Transacciones
Datos: Encrypt tarjetas si guardar (mejor no) | Mask licencias UI | Logs sin PII | GDPR
compliance
17. PERFORMANCE
Cache: Sessions, cache, queues sin Redis usar file/database driver | Available vehicles DB
query cada request con índices óptimos | User permissions eager load | Dashboard stats
calcular on-demand o cache 15min archivo
Database: Eager load relaciones | Chunk large queries | Index covering frecuentes | Explain
>100ms | Connection pooling 20
Assets: Vite build | Images WebP | Lazy load offscreen | Code splitting | Bundle <200KB
Queues: Horizon workers: 3 high, 2 default, 1 low | Emails low | Pagos high | GPS default |
Retry 3x
18. MONITOREO
Health: /health check DB, Queue, Disk | Ping 5min | Alert down >2 checks
Métricas: Response p95 <500ms | Error <1% | Queue wait <30s | Active vehicles |
Reservations/hour | Payments success
Alertas: Email + SMS críticos | Disk >85% | Queue >1000 pending | Error spike >5% |
Payment gateway down | MQTT disconnected
Logs: JSON structured | Request ID tracing | Channels: app, auth, payments, gps | Rotation
diaria | Retention 30d local, 90d remoto
19. DOCUMENTACIÓN
README: Setup <5min | Docker compose up | Seeders prueba | Usuarios test roles |
Variables ambiente
API: Sanctum endpoints | Postman collection | Request/response examples | Error codes
Wiki: Arquitectura C4 | Flujo reservas | MQTT integration | Webhook flow | Deployment |
Troubleshooting
Runbook: Crear admin | Suspender usuario | Refund manual | Reiniciar servicios | Restore
backup | Escalar workers
20. REGLAS DE ORO
1. Nunca modificar reserva confirmada sin transacción → Atomicidad crítica
2. Siempre verificar disponibilidad antes asignar → Lock pesimista DB
3. Webhook Mercadopago idempotente → Verificar transaction_id único
4. GPS timeout 90s marca conductor offline → Estado consistente
5. Cancelación <24h requiere aprobación → Regla negocio estricta
6. SuperAdmin acciones auditadas 100% → Compliance obligatorio
7. Emails en queue nunca síncronos → Performance crítica
8. Migrations testeadas staging antes prod → Zero downtime
9. Backup verificado antes deployment → Disaster recovery
10. Tests pass antes merge a main → Calidad código
11. Documentación actualizada antes deployment → Comprensión clara
12. Seguridad patches aplicados antes deployment → Protección datos
13. Monitoreo activo antes deployment → Rápida detección problemas
14. Alertas configuradas antes deployment → Immediate response
