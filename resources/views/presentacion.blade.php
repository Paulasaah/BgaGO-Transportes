<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    @include('partials.head')
</head>
<body class="bg-white dark:bg-zinc-800 antialiased" x-data="{ activeSection: 'proyecto' }">

    <!-- Header/Navigation -->
    <header class="sticky top-0 z-50 bg-white dark:bg-zinc-900 backdrop-blur-sm border-b border-zinc-200 dark:border-zinc-700">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700">
                        <x-app-logo-icon class="size-6 fill-current text-white" />
                    </span>
                </div>
                
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#proyecto" class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 transition">Proyecto</a>
                    <a href="#arquitectura" class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 transition">Arquitectura</a>
                    <a href="#stack" class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 transition">Stack</a>
                    <a href="#base-datos" class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 transition">Base de Datos</a>
                    <a href="#equipo" class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 transition">Equipo</a>
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition bg-blue-600 hover:bg-blue-700">
                        Ir a la Aplicación
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    
    <section class="relative py-20 overflow-hidden bg-gradient-to-br from-blue-600 to-blue-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-white">
                <h1 class="text-5xl md:text-6xl font-bold mb-6">
                    Sistema de Gestión de<br>
                    <span class="text-blue-200">Movilidad Urbana Inteligente</span>
                </h1>
                <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-4xl mx-auto leading-relaxed">
                    Plataforma web integral para gestión de servicios de transporte y logística urbana 
                    en el Área Metropolitana de Bucaramanga mediante Laravel con arquitectura MVC, 
                    autenticación multi-rol, geolocalización en tiempo real y telemetría IoT.
                </p>
                
                <div class="flex flex-wrap justify-center gap-4 mb-12">
                    <a href="{{ route('register') }}" class="px-6 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition shadow-lg">
                        Registrarse
                    </a>
                    <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.map') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg">
                        Mapa en Vivo
                    </a>
                    <a href="{{ route('admin.monitoring') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg">
                        Monitoreo
                    </a>
                    <a href="https://github.com/Paulasaah/BgaGO-Transportes" target="_blank" class="px-6 py-3 bg-gray-900 text-white rounded-lg font-semibold hover:bg-gray-800 transition shadow-lg inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                        Repositorio GitHub
                    </a>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="text-3xl font-bold mb-1">4</div>
                        <div class="text-sm text-blue-100">Sedes Metropolitanas</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="text-3xl font-bold mb-1">50+</div>
                        <div class="text-sm text-blue-100">Vehículos en Flota</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="text-3xl font-bold mb-1">3</div>
                        <div class="text-sm text-blue-100">Roles de Usuario</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="text-3xl font-bold mb-1">24/7</div>
                        <div class="text-sm text-blue-100">Telemetría GPS</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Objetivo del Proyecto -->
    <section id="proyecto" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Objetivo del Proyecto</h2>
                <div class="h-1 w-20 rounded-full" style="background-color: #1584de;"></div>
            </div>

            <div class="grid md:grid-cols-2 gap-12">
                <div>
                    <div class="prose prose-lg max-w-none">
                        <p class="text-gray-700 leading-relaxed mb-6">
                            BgaGO tiene como objetivo <strong>optimizar la movilidad en Bucaramanga y su área metropolitana</strong> 
                            mediante una red de cuatro sedes estratégicamente ubicadas y una flota diversa de vehículos 
                            disponibles para reserva y uso flexible.
                        </p>
                        <p class="text-gray-700 leading-relaxed mb-6">
                            El sistema permite tanto el <strong>alquiler directo en sede</strong> como el 
                            <strong>envío del vehículo a la ubicación del usuario</strong>, ofreciendo una experiencia 
                            cómoda y eficiente.
                        </p>
                        <p class="text-gray-700 leading-relaxed">
                            Además, integra un <strong>servicio de domicilios particulares</strong> que aprovecha la 
                            infraestructura logística y los conductores de la plataforma, fortaleciendo la conectividad 
                            y promoviendo una movilidad urbana más sostenible e inteligente.
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="border-l-4 border-blue-600 bg-blue-50 p-6 rounded-r-lg">
                        <h3 class="font-bold text-gray-900 mb-2">¿Qué?</h3>
                        <p class="text-gray-700 text-sm">Plataforma web integral para gestión de servicios de transporte y logística urbana</p>
                    </div>
                    <div class="border-l-4 border-green-600 bg-green-50 p-6 rounded-r-lg">
                        <h3 class="font-bold text-gray-900 mb-2">¿Cómo?</h3>
                        <p class="text-gray-700 text-sm">Mediante Laravel con arquitectura MVC, sistema multi-rol, geolocalización en tiempo real, pasarela de pagos y notificaciones automatizadas</p>
                    </div>
                    <div class="border-l-4 border-purple-600 bg-purple-50 p-6 rounded-r-lg">
                        <h3 class="font-bold text-gray-900 mb-2">¿Para qué?</h3>
                        <p class="text-gray-700 text-sm">Facilitar acceso a movilidad sostenible, optimizar gestión de flotas, coordinar domicilios y proporcionar herramientas administrativas</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Arquitectura -->
    <section id="arquitectura" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Arquitectura de la Solución</h2>
                <div class="h-1 w-20 rounded-full" style="background-color: #1584de;"></div>
                <p class="text-gray-600 mt-4">Arquitectura modular y escalable orientada a servicios</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 mb-12">
                <!-- Plataforma Web Principal -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: #1584de;">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Plataforma Web Principal</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Debian 12 en Azure VM con Docker, Apache y PHP</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-blue-600 mt-1">▸</span>
                            <span><strong>Interfaz Administrador:</strong> Gestión de usuarios, flota, sedes y reportes</span>
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-blue-600 mt-1">▸</span>
                            <span><strong>Interfaz Cliente:</strong> Registro, reservas, pagos y seguimiento</span>
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-blue-600 mt-1">▸</span>
                            <span><strong>Interfaz Conductor:</strong> Gestión de rutas y entregas</span>
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-blue-600 mt-1">▸</span>
                            <span><strong>Módulo Auth:</strong> Laravel Breeze + Spatie Permissions</span>
                        </li>
                    </ul>
                </div>

                <!-- API Gateway -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">API Gateway</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Punto de integración central con Laravel Sanctum</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-green-600 mt-1">▸</span>
                            <span>Interoperabilidad entre módulos</span>
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-green-600 mt-1">▸</span>
                            <span>Seguridad de comunicaciones</span>
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-green-600 mt-1">▸</span>
                            <span>Flujo estructurado de datos</span>
                        </li>
                    </ul>
                </div>

                <!-- Base de Datos -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Base de Datos MySQL</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Gestión relacional con Eloquent ORM</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-orange-600 mt-1">▸</span>
                            <span>Usuarios, reservas y vehículos</span>
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-orange-600 mt-1">▸</span>
                            <span>Transacciones y pagos</span>
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-orange-600 mt-1">▸</span>
                            <span>Registros de actividad y telemetría</span>
                        </li>
                    </ul>
                </div>

                <!-- Servicios IoT -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Telemetría GPS (MQTT)</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Mosquitto MQTT para monitoreo IoT en tiempo real</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-purple-600 mt-1">▸</span>
                            <span><strong>Publisher:</strong> Genera datos de telemetría GPS</span>
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-purple-600 mt-1">▸</span>
                            <span><strong>Subscriber:</strong> Envía datos al controlador</span>
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-purple-600 mt-1">▸</span>
                            <span>Rastreo de vehículos y domicilios</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Pasarela de Pago -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold mb-2">Pasarela de Pago: MercadoPago</h3>
                        <p class="text-blue-100">Procesamiento seguro de transacciones para reservas, pagos por uso y servicios de domicilio</p>
                    </div>
                    <svg class="w-16 h-16 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Stack Tecnológico -->
    <section id="stack" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Stack Tecnológico</h2>
                <div class="h-1 w-20 rounded-full" style="background-color: #1584de;"></div>
            </div>

            <div class="grid md:grid-cols-4 gap-6">
                <!-- Backend -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 border border-red-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 bg-red-600 rounded-full"></span>
                        Backend
                    </h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li>• Laravel 12+ (PHP 8.2+)</li>
                        <li>• Eloquent ORM</li>
                        <li>• MySQL 8.0+</li>
                        <li>• Spatie Permissions</li>
                        <li>• Mosquitto (MQTT)</li>
                    </ul>
                </div>

                <!-- Frontend -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                        Frontend
                    </h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li>• Blade Templates</li>
                        <li>• Tailwind CSS v3</li>
                        <li>• Alpine.js</li>
                        <li>• Livewire</li>
                        <li>• Flux UI Components</li>
                        <li>• Chart.js</li>
                    </ul>
                </div>

                <!-- Integraciones -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-600 rounded-full"></span>
                        Integraciones
                    </h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li>• Leaflet.js (Mapas)</li>
                        <li>• MercadoPago</li>
                        <li>• Laravel Mail</li>
                    </ul>
                </div>

                <!-- Infraestructura -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 bg-purple-600 rounded-full"></span>
                        Infraestructura
                    </h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li>• Debian 12 (Azure VM)</li>
                        <li>• Apache + PHP</li>
                        <li>• Docker</li>
                        <li>• Git & GitHub</li>
                        <li>• GitHub Actions CI/CD</li>
                    </ul>
                </div>
            </div>

            <!-- Herramientas -->
            <div class="mt-8 bg-gray-50 rounded-xl p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Herramientas de Desarrollo</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="flex items-center gap-2 text-sm text-gray-700">
                        <span class="text-blue-600">→</span> Composer (PHP)
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-700">
                        <span class="text-blue-600">→</span> pnpm (JavaScript)
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-700">
                        <span class="text-blue-600">→</span> Postman (API Testing)
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-700">
                        <span class="text-blue-600">→</span> Laravel Sanctum (Tokens)
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Base de Datos -->
    <section id="base-datos" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Estructura de Base de Datos</h2>
                <div class="h-1 w-20 rounded-full" style="background-color: #1584de;"></div>
                <p class="text-gray-600 mt-4">MySQL 8.0+ con arquitectura relacional y Eloquent ORM</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Migraciones -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                        </svg>
                        Migraciones (15 tablas)
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-gray-700">users</span>
                            <span class="text-xs text-gray-500">1.5k</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-gray-700">branches</span>
                            <span class="text-xs text-gray-500">914b</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-gray-700">vehicles</span>
                            <span class="text-xs text-gray-500">1.6k</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-gray-700">driver_profiles</span>
                            <span class="text-xs text-gray-500">808b</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-gray-700">reservations</span>
                            <span class="text-xs text-gray-500">3.4k</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-gray-700">payments</span>
                            <span class="text-xs text-gray-500">1.6k</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-gray-700">gps_tracks</span>
                            <span class="text-xs text-gray-500">2.3k</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-gray-700">telemetrias</span>
                            <span class="text-xs text-gray-500">2.3k</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-gray-700">deliveries</span>
                            <span class="text-xs text-gray-500">1.4k</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-gray-700">vehicle_maintenances</span>
                            <span class="text-xs text-gray-500">3.0k</span>
                        </div>
                    </div>
                </div>

                <!-- Modelos Eloquent -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                        Modelos Eloquent (13 modelos)
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-center p-2 bg-orange-50 rounded">
                            <span class="text-gray-700">User.php</span>
                            <span class="text-xs text-gray-500">1.3k</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-orange-50 rounded">
                            <span class="text-gray-700">Branch.php</span>
                            <span class="text-xs text-gray-500">1.4k</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-orange-50 rounded">
                            <span class="text-gray-700">Vehicle.php</span>
                            <span class="text-xs text-gray-500">1.0k</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-orange-50 rounded">
                            <span class="text-gray-700">DriverProfile.php</span>
                            <span class="text-xs text-gray-500">725b</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-orange-50 rounded">
                            <span class="text-gray-700">Reservation.php</span>
                            <span class="text-xs text-gray-500">1.7k</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-orange-50 rounded">
                            <span class="text-gray-700">Payment.php</span>
                            <span class="text-xs text-gray-500">876b</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-orange-50 rounded">
                            <span class="text-gray-700">Telemetria.php</span>
                            <span class="text-xs text-gray-500">2.6k</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-orange-50 rounded">
                            <span class="text-gray-700">Delivery.php</span>
                            <span class="text-xs text-gray-500">1.0k</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-orange-50 rounded">
                            <span class="text-gray-700">VehicleMaintenance.php</span>
                            <span class="text-xs text-gray-500">1.1k</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Relaciones -->
            <div class="mt-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-6 text-white">
                <h3 class="text-xl font-bold mb-4">Relaciones entre Entidades</h3>
                <div class="grid md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                        <strong>User → Reservations</strong>
                        <p class="text-blue-100 mt-1">hasMany (1:N)</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                        <strong>Vehicle → Reservations</strong>
                        <p class="text-blue-100 mt-1">hasMany (1:N)</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                        <strong>Branch → Vehicles</strong>
                        <p class="text-blue-100 mt-1">hasMany (1:N)</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                        <strong>Driver → Deliveries</strong>
                        <p class="text-blue-100 mt-1">hasMany (1:N)</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                        <strong>Reservation → Payment</strong>
                        <p class="text-blue-100 mt-1">hasOne (1:1)</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                        <strong>Vehicle → Telemetria</strong>
                        <p class="text-blue-100 mt-1">hasMany (1:N)</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Funcionalidades Principales -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Funcionalidades Principales</h2>
                <div class="h-1 w-20 rounded-full" style="background-color: #1584de;"></div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <!-- Gestión de Usuarios -->
                <div class="bg-white rounded-xl p-6 shadow-sm border-2 border-gray-200 hover:border-blue-500 transition">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Sistema Multi-Rol</h3>
                    <p class="text-sm text-gray-600 mb-4">3 roles con permisos granulares mediante Spatie Permissions</p>
                    <ul class="space-y-1 text-xs text-gray-600">
                        <li>• <strong>Usuario:</strong> Registro, reservas y pagos</li>
                        <li>• <strong>Conductor:</strong> Gestión de entregas y rutas</li>
                        <li>• <strong>Administrador:</strong> Control total del sistema</li>
                    </ul>
                    <a href="{{ route('admin.users.index') }}" class="mt-4 inline-block text-sm text-blue-600 hover:text-blue-700 font-medium">
                        Ver usuarios →
                    </a>
                </div>

                <!-- Gestión de Flotas -->
                <div class="bg-white rounded-xl p-6 shadow-sm border-2 border-gray-200 hover:border-green-500 transition">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Gestión de Vehículos</h3>
                    <p class="text-sm text-gray-600 mb-4">Control completo de flota y disponibilidad en tiempo real</p>
                    <ul class="space-y-1 text-xs text-gray-600">
                        <li>• Motos, bicicletas y patinetas eléctricas</li>
                        <li>• Estado y mantenimiento predictivo</li>
                        <li>• Asignación automática por sede</li>
                    </ul>
                    <a href="{{ route('admin.vehicles.index') }}" class="mt-4 inline-block text-sm text-green-600 hover:text-green-700 font-medium">
                        Ver vehículos →
                    </a>
                </div>

                <!-- Sistema de Reservas -->
                <div class="bg-white rounded-xl p-6 shadow-sm border-2 border-gray-200 hover:border-purple-500 transition">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Sistema de Reservas</h3>
                    <p class="text-sm text-gray-600 mb-4">Reserva anticipada con verificación de disponibilidad</p>
                    <ul class="space-y-1 text-xs text-gray-600">
                        <li>• Reserva directa en sede o delivery</li>
                        <li>• Confirmación automática por email</li>
                        <li>• Integración con MercadoPago</li>
                    </ul>
                    <a href="{{ route('admin.reservations.index') }}" class="mt-4 inline-block text-sm text-purple-600 hover:text-purple-700 font-medium">
                        Ver reservas →
                    </a>
                </div>

                <!-- Domicilios -->
                <div class="bg-white rounded-xl p-6 shadow-sm border-2 border-gray-200 hover:border-orange-500 transition">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Servicio de Domicilios</h3>
                    <p class="text-sm text-gray-600 mb-4">Mensajería urbana con conductores verificados</p>
                    <ul class="space-y-1 text-xs text-gray-600">
                        <li>• Asignación automática de conductor</li>
                        <li>• Cálculo de tarifa por distancia</li>
                        <li>• Rastreo GPS en tiempo real</li>
                    </ul>
                    <a href="{{ route('dashboard') }}" class="mt-4 inline-block text-sm text-orange-600 hover:text-orange-700 font-medium">
                        Crear domicilio →
                    </a>
                </div>

                <!-- Telemetría GPS -->
                <div class="bg-white rounded-xl p-6 shadow-sm border-2 border-gray-200 hover:border-red-500 transition">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Telemetría en Tiempo Real</h3>
                    <p class="text-sm text-gray-600 mb-4">Monitoreo IoT con protocolo MQTT (Mosquitto)</p>
                    <ul class="space-y-1 text-xs text-gray-600">
                        <li>• Publisher: Simula dispositivos GPS</li>
                        <li>• Subscriber: Almacena en base de datos</li>
                        <li>• Visualización en Leaflet.js</li>
                    </ul>
                    <a href="{{ route('admin.map') }}" class="mt-4 inline-block text-sm text-red-600 hover:text-red-700 font-medium">
                        Ver mapa GPS →
                    </a>
                </div>

                <!-- Reportes y Analytics -->
                <div class="bg-white rounded-xl p-6 shadow-sm border-2 border-gray-200 hover:border-indigo-500 transition">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Reportes y Analytics</h3>
                    <p class="text-sm text-gray-600 mb-4">Dashboards interactivos con Chart.js</p>
                    <ul class="space-y-1 text-xs text-gray-600">
                        <li>• Métricas de uso y rendimiento</li>
                        <li>• Reportes financieros</li>
                        <li>• Exportación a Excel/PDF</li>
                    </ul>
                    <a href="{{ route('admin.reports.index') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                        Ver reportes →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Equipo -->
    <section id="equipo" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Equipo de Desarrollo</h2>
                <div class="h-1 w-20 rounded-full" style="background-color: #1584de;"></div>
                <p class="text-gray-600 mt-4">Proyecto Final - Desarrollo y Arquitectura Backend</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 mb-12">
                <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-200 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-white font-bold text-2xl">MP</span>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Maria Paula Saavedra Martinez</h3>
                    <p class="text-sm text-gray-600">Desarrolladora Backend</p>
                </div>

                <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-200 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-700 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-white font-bold text-2xl">SC</span>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Santiago Cardona Prada</h3>
                    <p class="text-sm text-gray-600">Desarrollador Backend</p>
                </div>

                <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-200 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-700 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-white font-bold text-2xl">SA</span>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Sergio Alejandro Amaya Corzo</h3>
                    <p class="text-sm text-gray-600">Desarrollador Backend</p>
                </div>
            </div>

            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-8 text-white text-center">
                <h3 class="text-2xl font-bold mb-2">Docente: Mg. Fabián Suárez</h3>
                <p class="text-blue-100 mb-4">Desarrollo y Arquitectura Backend</p>
                <p class="text-sm text-blue-200">Universidad Autónoma de Bucaramanga - UNAB 2025</p>
            </div>

            <!-- Agradecimientos -->
            <div class="mt-8 bg-white rounded-xl p-8 shadow-sm border border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 mb-4 text-center">Agradecimientos</h3>
                <p class="text-gray-700 text-center max-w-2xl mx-auto">
                    Queremos agradecer a Dios todo poderoso, a nuestro Señor y Salvador Jesucristo, 
                    y a nuestro profesor y mentor Mg. Fabián Suárez. Este proyecto fue posible gracias 
                    a la ayuda y guía brindada por ellos.
                </p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="font-bold text-lg mb-4">BgaGO Transportes</h3>
                    <p class="text-gray-400 text-sm">Sistema de gestión de movilidad urbana inteligente para el Área Metropolitana de Bucaramanga</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Plataforma</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-white">Dashboard</a></li>
                        <li><a href="{{ route('catalog.index') }}" class="hover:text-white">Catálogo</a></li>
                        <li><a href="{{ route('admin.map') }}" class="hover:text-white">Mapa GPS</a></li>
                        <li><a href="{{ route('admin.monitoring') }}" class="hover:text-white">Monitoreo</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Administración</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('admin.users.index') }}" class="hover:text-white">Usuarios</a></li>
                        <li><a href="{{ route('admin.drivers.index') }}" class="hover:text-white">Conductores</a></li>
                        <li><a href="{{ route('admin.vehicles.index') }}" class="hover:text-white">Vehículos</a></li>
                        <li><a href="{{ route('admin.reservations.index') }}" class="hover:text-white">Reservas</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Recursos</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="https://github.com/Paulasaah/BgaGO-Transportes" target="_blank" class="hover:text-white">GitHub</a></li>
                        <li><a href="{{ route('admin.reports.index') }}" class="hover:text-white">Reportes</a></li>
                        <li><a href="#proyecto" class="hover:text-white">Documentación</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-400">
                <p>&copy; 2025 BgaGO Transportes - Universidad Autónoma de Bucaramanga</p>
                <p class="mt-2">Proyecto Final - Desarrollo y Arquitectura Backend</p>
            </div>
        </div>
    </footer>

</body>
</html>