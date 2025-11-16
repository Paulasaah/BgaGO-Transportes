# 🚗 BgaGO - Sistema de Transporte

Sistema integrado de gestión de transporte con telemetría IoT, reservas y domicilios.

## 🚀 Stack Tecnológico

- **Backend:** Laravel 12 + Livewire + Volt
- **Frontend:** Tailwind CSS + Flux UI
- **Base de Datos:** MySQL / SQLite
- **IoT:** MQTT (Mosquitto)
- **Pagos:** Mercado Pago
- **Autenticación:** Laravel Sanctum
- **Permisos:** Spatie Permission
- **WebSockets:** Laravel Reverb

## 📦 Instalación

### Requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+ o SQLite
- Extensión PHP SQLite3 (para tests)

### Pasos

```bash
# 1. Clonar repositorio
git clone <repository-url>
cd BgaGO

# 2. Instalar extensión SQLite (requerido para tests)
sudo apt-get install php8.2-sqlite3  # Ubuntu/Debian
# O para otras distros:
# sudo dnf install php-sqlite3        # Fedora/RHEL
# sudo pacman -S php-sqlite            # Arch Linux

# 3. Instalar dependencias PHP
composer install

# 4. Instalar dependencias Node
npm install

# 5. Configurar entorno
cp .env.example .env
php artisan key:generate

# 6. Configurar base de datos
# Editar .env con tus credenciales de BD

# 7. Ejecutar migraciones
php artisan migrate --seed

# 8. Compilar assets
npm run build

# 9. Iniciar servidor
php artisan serve
```

## 🧪 Testing

### Setup de Base de Datos para Tests

**Opción 1: MySQL (Configuración Actual)**
```bash
# Iniciar MySQL
sudo systemctl start mysql

# Crear base de datos de testing
./scripts/setup-testing-db.sh

# Ejecutar tests
php artisan test
```

**Opción 2: SQLite (Más Rápido - Recomendado para Desarrollo)**
```bash
# Instalar extensión SQLite (una sola vez)
sudo apt-get install php8.2-sqlite3

# Ejecutar tests (10x más rápido)
php artisan test --configuration=phpunit.sqlite.xml
```

### Ejecutar Tests

```bash
# Con MySQL (default)
php artisan test

# Con SQLite (rápido)
php artisan test --configuration=phpunit.sqlite.xml

# Tests específicos
php artisan test --filter=Reservation
php artisan test --filter=Payment
php artisan test --filter=Delivery

# Solo tests unitarios
php artisan test --testsuite=Unit

# Solo tests feature
php artisan test --testsuite=Feature
```

### Verificar mejoras implementadas
```bash
./scripts/verify-improvements.sh
```

> **💡 Tip:** Usa SQLite para desarrollo diario (más rápido) y MySQL antes de deploy (verificar compatibilidad).  
> Ver documentación completa en `.windsurf/MYSQL-VS-SQLITE-TESTING.md`

## 🔧 Configuración

### Variables de Entorno Importantes

```env
# App
APP_NAME=BgaGO
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Base de Datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bgago
DB_USERNAME=root
DB_PASSWORD=

# MQTT
MQTT_HOST=127.0.0.1
MQTT_PORT=1883
MQTT_CONNECT_TIMEOUT=5
MQTT_SOCKET_TIMEOUT=10

# HTTP Timeouts
HTTP_TIMEOUT=30
HTTP_CONNECT_TIMEOUT=10
HTTP_RETRY_TIMES=3

# Mercado Pago
MERCADOPAGO_ACCESS_TOKEN=your_token
MERCADOPAGO_PUBLIC_KEY=your_key
MERCADOPAGO_TIMEOUT=15

# Reverb (WebSockets)
REVERB_APP_ID=bgago-app
REVERB_APP_KEY=bgago-key
REVERB_APP_SECRET=bgago-secret
REVERB_HOST=0.0.0.0
REVERB_PORT=6001
```

## 📚 Documentación

La documentación completa está en `.windsurf/`:

- **[START-HERE.md](.windsurf/START-HERE.md)** - Guía de inicio
- **[context.md](.windsurf/context.md)** - Contexto del proyecto
- **[development-guide.md](.windsurf/development-guide.md)** - Guía de desarrollo
- **[MEJORAS-URGENTES-IMPLEMENTADAS.md](.windsurf/MEJORAS-URGENTES-IMPLEMENTADAS.md)** - Mejoras recientes

## 🔐 Roles y Permisos

### Roles Disponibles
- **super_admin** - Acceso total
- **admin** - Administración general
- **conductor** - Gestión de servicios asignados
- **cliente** - Creación de reservas y domicilios

### Permisos Principales
- `view-telemetry` - Ver telemetría de vehículos
- `manage-payments` - Gestionar pagos
- `crear_reservas` - Crear reservas

## 🛣️ Rutas Principales

### API
- `GET /api/status` - Estado del API
- `GET /api/vehicles` - Catálogo de vehículos
- `POST /api/reservations` - Crear reserva (auth)
- `POST /api/deliveries/package` - Crear domicilio (auth)
- `POST /api/payments/{payment}/process` - Procesar pago (auth)

### Web
- `/` - Página principal
- `/dashboard` - Dashboard de usuario (auth)
- `/admin/dashboard` - Dashboard administrativo (admin)
- `/catalog` - Catálogo de vehículos (auth)

## 🔥 Características Principales

### ✅ Implementado
- Sistema de reservas de vehículos
- Gestión de domicilios (paquetes y vehículos)
- Telemetría IoT en tiempo real (MQTT)
- Procesamiento de pagos (Mercado Pago)
- Dashboard administrativo con Livewire
- API REST con autenticación
- Rate limiting en endpoints críticos
- Timeouts configurables para servicios externos
- 39 tests automatizados

### 🚧 En Desarrollo
- Integración con Azure IoT Central
- Notificaciones push
- Reportes avanzados
- App móvil

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

### Estándares de Código
- Seguir PSR-12
- Tests para nuevas funcionalidades
- Documentar cambios importantes

## 📝 Comandos Útiles

```bash
# Desarrollo
composer dev                    # Iniciar servidor + queue + logs + vite

# Base de datos
php artisan migrate:fresh --seed  # Resetear BD con datos de prueba
php artisan db:seed              # Solo seeders

# Caché
php artisan optimize:clear       # Limpiar todas las cachés
php artisan config:cache         # Cachear configuración
php artisan route:cache          # Cachear rutas

# Queue
php artisan queue:work           # Procesar trabajos en cola
php artisan queue:listen         # Escuchar trabajos

# MQTT
php artisan mqtt:listen          # Escuchar mensajes MQTT

# Logs
php artisan pail                 # Ver logs en tiempo real
```

## 🐛 Debugging

### Logs
```bash
# Ver logs en tiempo real
php artisan pail

# Ver logs específicos
tail -f storage/logs/laravel.log
```

### Tinker
```bash
php artisan tinker

# Ejemplos
>>> User::count()
>>> Reservation::where('estado', 'pendiente')->get()
>>> Vehicle::factory()->create()
```

## 📊 Métricas

- **Tests:** 39 (28 Feature + 11 Unit)
- **Cobertura:** ~60% (objetivo: >80%)
- **Endpoints API:** 50+
- **Modelos:** 14
- **Migraciones:** 18

## 📞 Soporte

Para problemas o preguntas:
1. Revisar documentación en `.windsurf/`
2. Ejecutar `./scripts/verify-improvements.sh`
3. Revisar logs con `php artisan pail`
4. Crear un issue en el repositorio

## 📄 Licencia

Este proyecto es privado y confidencial.

---

**Versión:** 2.0  
**Última actualización:** Noviembre 2024
