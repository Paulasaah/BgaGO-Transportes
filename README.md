# 🚍 BgaGO Transportes

![Laravel](https://img.shields.io/badge/Laravel-12.x-ff2d20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3.x-purple?style=for-the-badge&logo=laravel)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38bdf8?style=for-the-badge&logo=tailwind-css&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-blue?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-00758f?style=for-the-badge&logo=mysql)
![pnpm](https://img.shields.io/badge/pnpm-9.x-f69220?style=for-the-badge&logo=pnpm)
![Azure](https://img.shields.io/badge/Deployed%20on-Azure-0078D4?style=for-the-badge&logo=microsoft-azure)

> **BgaGO Transportes** es una plataforma web moderna para la gestión de transporte urbano y reservas en el Área Metropolitana de Bucaramanga. Desarrollada con **Laravel**, **Livewire** y **Tailwind CSS**, optimizada para entornos Azure.

---

## Tabla de Contenidos

- [Stack Tecnológico](#-stack-tecnológico)
- [Instalación y Configuración](#️-instalación-y-configuración)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Módulos Principales](#-módulos-principales)
- [Flujo de Trabajo del Equipo](#-flujo-de-trabajo-del-equipo)
- [Testing](#-testing)
- [Despliegue en Azure](#️-despliegue-en-azure)
- [Paneles y Funcionalidades](#-paneles-y-funcionalidades)
- [Roadmap](#-roadmap)
- [Equipo de Desarrollo](#-equipo-de-desarrollo)
- [Licencia](#-licencia)

---

## Stack Tecnológico

### Backend
- **Laravel 12+ (PHP 8.2+)**
- **Eloquent ORM**
- **MySQL 8.0+**
- **Spatie Laravel Permission** - Roles y permisos
- **Laravel Horizon** - Gestión de colas
- **Laravel Telescope** - Monitoreo y debugging avanzado

### Frontend
- **Blade Templates**
- **Tailwind CSS v3**
- **Alpine.js**
- **Livewire** - Componentes reactivos
- **Flux UI** - Starter Kit UI oficial de Laravel + Livewire
- **Chart.js** - Dashboards y reportes dinámicos

### Integraciones
- **Leaflet.js** - Mapas interactivos y geolocalización
- **PayU Colombia** - Pasarela de pagos
- **Mailtrap (dev)** / **Amazon SES (prod)** - Envío de correos
- **Pusher / Laravel Echo Server** - WebSockets en tiempo real

### Herramientas
- **Composer** - Dependencias PHP  
- **pnpm** - Dependencias JS  
- **Postman** - Pruebas API  
- **PHPUnit** - Testing  
- **Git & GitHub** - Control de versiones  
- **GitHub Actions** - CI/CD  
- **Redis** - Caché y colas  

### ☁️ Infraestructura
- **Debian 12 (Azure VM)**
- **Apache / Nginx (PHP-FPM)**
- **Docker**
- **Redis** para sesiones y colas

---

## ⚙️ Instalación y Configuración

### 🧰 Requisitos Previos

| Dependencia | Versión mínima | Verificar con |
|-------------|----------------|---------------|
| PHP | 8.2 | `php -v` |
| Composer | 2.6 | `composer -V` |
| Node.js | 20+ | `node -v` |
| pnpm | 9+ | `pnpm -v` |
| MySQL | 8.0+ | `mysql --version` |
| Git | Última | `git --version` |

---

### Paso 1: Clonar el proyecto

```bash
git clone https://github.com/Paulasaah/BgaGO-Transportes.git
cd BgaGO-Transportes
```

### Paso 2: Configurar entorno

```bash
cp .env.example .env
php artisan key:generate
```

Edita el archivo `.env` con tus credenciales locales:

```env
APP_NAME="BgaGO"
APP_ENV=local
APP_KEY=base64:...
APP_URL=http://localhost:9001

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bgago_transportes
DB_USERNAME=root
DB_PASSWORD=tu_contraseña

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Paso 3: Instalar dependencias

```bash
# Dependencias PHP
composer install

# Dependencias frontend
pnpm install
```

### Paso 4: Migrar base de datos

```bash
php artisan migrate --seed
```

Esto creará las tablas iniciales y roles base (Admin, Usuario, Conductor).

### Paso 5: Compilar assets

```bash
# Modo desarrollo
pnpm run dev

# Modo producción
pnpm run build
```

### Paso 6: Ejecutar servidor local 

```bash
php artisan serve 
```

---

## 🧱 Estructura del Proyecto

```
BgaGO-Transportes/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Controladores MVC
│   │   └── Livewire/          # Componentes Livewire reactivos
│   ├── Models/                # Modelos Eloquent
│   ├── Services/              # Servicios personalizados y MockData
│   ├── Providers/             # Service Providers
│   └── Policies/              # Políticas de autorización
│
├── database/
│   ├── migrations/            # Estructura de tablas
│   ├── seeders/               # Datos iniciales
│   └── factories/             # Datos de prueba
│
├── public/                    # Archivos públicos y build
│
├── resources/
│   ├── views/                 # Vistas Blade + Livewire
│   │   ├── components/        # Componentes parciales
│   │   ├── layouts/           # Layouts principales
│   │   └── landing/           # Páginas públicas
│   ├── css/                   # Estilos base Tailwind
│   └── js/                    # Scripts Alpine + Livewire + Charts
│
├── routes/
│   ├── web.php                # Rutas principales
│   ├── api.php                # Endpoints API
│   └── channels.php           # WebSockets
│
├── tests/                     # Pruebas PHPUnit
├── vite.config.js             # Configuración Vite
├── package.json               # Configuración frontend
├── composer.json              # Dependencias backend
└── README.md
```

---

## Módulos Principales

| Módulo | Descripción | Tablas / Lógica |
|--------|-------------|-----------------|
| **Usuarios y Roles** | Registro, login, permisos y roles | `users`, `roles`, `model_has_roles` |
| **Vehículos** | Gestión de motos, scooters y bicicletas | `vehicles` |
| **Sedes** | Información y disponibilidad de estaciones | `sedes` |
| **Reservas y Préstamos** | Flujo de reservas, control de tiempos y pagos | `reservas`, `prestamos` |
| **Pagos** | Integración con PayU, cupones y transacciones | `pagos`, `cupones` |
| **Telemetría GPS** | Monitoreo en tiempo real | `telemetria_gps` |
| **Notificaciones** | Alertas, correos y avisos del sistema | `notifications` |
| **Panel Admin** | Dashboards y métricas con Chart.js | `reports` |

---

## Flujo de Trabajo del Equipo

El proyecto se desarrolla bajo el modelo **Git Flow**, con trabajo colaborativo entre tres miembros.

| Rol | Nombre | Responsabilidad |
|-----|--------|-----------------|
| 🧠 **Backend Lead** | Santiago Cardona Prada | Arquitectura Laravel, base de datos, integraciones y APIs |
| 🎨 **Frontend Lead** | Paula Saavedra | UI/UX, Blade + Livewire, Tailwind, vistas públicas |
| 🔍 **QA / Infraestructura** | Emily Nicol David | Pruebas, documentación, CI/CD y despliegue en Azure |

### Flujo Git

```bash
# Crear rama de trabajo
git checkout -b feature/nueva-funcionalidad

# Guardar cambios
git add .
git commit -m "feat: agregar módulo de reservas"

# Subir al remoto
git push origin feature/nueva-funcionalidad

# Crear Pull Request y revisión antes del merge a main
```

---

## Testing

```bash
# Ejecutar pruebas unitarias
php artisan test

# O con PHPUnit directamente
vendor/bin/phpunit
```

---

## ☁️ Despliegue en Azure

### Pasos para producción en Debian 12 (Azure VM)

1. **Clonar el proyecto en la VM:**
```bash
mkdir proyecto
cd proyecto
git clone https://github.com/Paulasaah/BgaGO-Transportes.git
```

2. **Instalar dependencias con Composer y pnpm:**
```bash
composer install --optimize-autoloader --no-dev
pnpm install
pnpm run build
```

3. **Configurar Apache**
```bash
# Editar archivo de configuración
sudo nano /etc/apache2/sites-available/bgago.conf
```

4. **Editar `.env` con credenciales de producción:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://bgago.com
```

5. **Ejecutar migraciones:**
```bash
php artisan migrate --seed --force
```

6. **Configurar Redis, Horizon y Supervisor:**
```bash
sudo systemctl restart apache2
sudo systemctl restart redis-server
php artisan horizon
```

---

## 📊 Paneles y Funcionalidades

- **Mapa interactivo (Leaflet.js)** - Rastreo en tiempo real
- **Dashboard Admin** - Métricas con Chart.js
- **Panel Usuario** - Reservas, historial y notificaciones
- **Gestión de pagos (PayU)** - Cálculo por tiempo/distancia/zonas
- **Sistema de colas (Redis + Horizon)** - Optimización de tareas

---

## Roadmap

- [ ] Módulo de auditoría y logs
- [ ] Integración IoT con Azure Central
- [ ] WebSockets para tracking en tiempo real
- [ ] App móvil (Flutter / React Native)
- [ ] Microservicios para pagos y GPS

---

## Equipo de Desarrollo

| Nombre | Rol | GitHub |
|--------|-----|--------|
| **Santiago Cardona Prada** | Backend & Arquitectura |  |
| **Paula Saavedra** | Frontend & UI/UX | [@Paulasaah](https://github.com/Paulasaah) |
| **Sergio Alejandro** | QA & Infraestructura | — |

---

## Licencia

Este proyecto es de uso académico y formativo.
