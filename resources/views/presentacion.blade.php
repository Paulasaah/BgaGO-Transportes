<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    @include('partials.head')
</head>
<body class="bg-gray-50 antialiased" x-data="{ activeSection: 'proyecto', mobileMenuOpen: false }">

    <!-- Header/Navigation -->
    <header class="fixed w-full top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200 shadow-sm">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-700">
                        <span class="text-white font-bold text-lg">B</span>
                    </div>
                    <div>
                        <h1 class="font-bold text-gray-900">BgaGO</h1>
                        <p class="text-xs text-gray-500">Backend - UNAB 2025</p>
                    </div>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#proyecto" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Proyecto</a>
                    <a href="#arquitectura" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Arquitectura</a>
                    <a href="#stack" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Stack</a>
                    <a href="#base-datos" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Base de Datos</a>
                    <a href="#equipo" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Equipo</a>
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-all bg-blue-600 hover:bg-blue-700 hover:shadow-lg">
                        Ir a la Aplicación
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" x-cloak class="md:hidden py-4 space-y-2">
                <a href="#proyecto" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 rounded">Proyecto</a>
                <a href="#arquitectura" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 rounded">Arquitectura</a>
                <a href="#stack" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 rounded">Stack</a>
                <a href="#base-datos" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 rounded">Base de Datos</a>
                <a href="#equipo" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 rounded">Equipo</a>
                <a href="{{ route('dashboard') }}" class="block px-4 py-2 bg-blue-600 text-white rounded-lg text-center">Ir a la Aplicación</a>
            </div>
        </nav>
    </header>

    <!-- Hero Section usando el componente landing.hero -->
    <x-landing.hero
        title='Sistema de Gestión de<br><span class="text-blue-700">Movilidad Urbana Inteligente</span>'
        subtitle="Plataforma web integral para gestión de servicios de transporte y logística urbana en el Área Metropolitana de Bucaramanga mediante Laravel con arquitectura MVC, autenticación multi-rol, geolocalización en tiempo real y telemetría IoT."
        :primaryCta="[
            'text' => 'Comenzar Ahora',
            'route' => 'register'
        ]"
        :secondaryCta="[
            'text' => 'Ver Dashboard',
            'route' => 'dashboard'
        ]"
        :stats="[
            ['value' => '4', 'label' => 'Sedes Metropolitanas'],
            ['value' => '50+', 'label' => 'Vehículos en Flota'],
            ['value' => '3', 'label' => 'Roles de Usuario'],
            ['value' => '24/7', 'label' => 'Telemetría GPS']
        ]"
    />

    <!-- Quick Actions Bar -->
    <section class="py-8 bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100 transition-all hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.map') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-100 transition-all hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    Mapa GPS
                </a>
                <a href="{{ route('admin.monitoring') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 rounded-lg text-sm font-medium hover:bg-green-100 transition-all hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Monitoreo
                </a>
                <a href="https://github.com/Paulasaah/BgaGO-Transportes" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-gray-800 transition-all hover:scale-105">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                    </svg>
                    GitHub
                </a>
            </div>
        </div>
    </section>

    <!-- Objetivo del Proyecto -->
    <section id="proyecto" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12" x-data="{ show: false }" x-intersect="show = true">
                <div :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" class="transition-all duration-700">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Objetivo del Proyecto</h2>
                    <div class="h-1 w-20 rounded-full bg-blue-600"></div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-12">
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-8'" class="transition-all duration-700 delay-100">
                    <div class="prose prose-lg max-w-none">
                        <p class="text-gray-700 leading-relaxed mb-6">
                            BgaGO tiene como objetivo <strong class="text-blue-700">optimizar la movilidad en Bucaramanga y su área metropolitana</strong> 
                            mediante una red de cuatro sedes estratégicamente ubicadas y una flota diversa de vehículos 
                            disponibles para reserva y uso flexible.
                        </p>
                        <p class="text-gray-700 leading-relaxed mb-6">
                            El sistema permite tanto el <strong class="text-blue-700">alquiler directo en sede</strong> como el 
                            <strong class="text-blue-700">envío del vehículo a la ubicación del usuario</strong>, ofreciendo una experiencia 
                            cómoda y eficiente.
                        </p>
                        <p class="text-gray-700 leading-relaxed">
                            Además, integra un <strong class="text-blue-700">servicio de domicilios particulares</strong> que aprovecha la 
                            infraestructura logística y los conductores de la plataforma, fortaleciendo la conectividad 
                            y promoviendo una movilidad urbana más sostenible e inteligente.
                        </p>
                    </div>
                </div>

                <div class="space-y-4" x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-8'" class="transition-all duration-700 delay-200">
                    <div class="border-l-4 border-blue-600 bg-blue-50 p-6 rounded-r-lg hover:shadow-lg transition-all duration-300 hover:translate-x-2">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-2">¿Qué?</h3>
                                <p class="text-gray-700 text-sm">Plataforma web integral para gestión de servicios de transporte y logística urbana</p>
                            </div>
                        </div>
                    </div>
                    <div class="border-l-4 border-green-600 bg-green-50 p-6 rounded-r-lg hover:shadow-lg transition-all duration-300 hover:translate-x-2">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-2">¿Cómo?</h3>
                                <p class="text-gray-700 text-sm">Mediante Laravel con arquitectura MVC, sistema multi-rol, geolocalización en tiempo real, pasarela de pagos y notificaciones automatizadas</p>
                            </div>
                        </div>
                    </div>
                    <div class="border-l-4 border-purple-600 bg-purple-50 p-6 rounded-r-lg hover:shadow-lg transition-all duration-300 hover:translate-x-2">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-purple-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-2">¿Para qué?</h3>
                                <p class="text-gray-700 text-sm">Facilitar acceso a movilidad sostenible, optimizar gestión de flotas, coordinar domicilios y proporcionar herramientas administrativas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Arquitectura -->
    <section id="arquitectura" class="py-20 bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-16 text-center" x-data="{ show: false }" x-intersect="show = true">
                <div :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" class="transition-all duration-700">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Arquitectura de la Solución</h2>
                    <div class="h-1 w-20 rounded-full bg-blue-600 mx-auto mb-4"></div>
                    <p class="text-gray-600 text-lg">Arquitectura modular y escalable orientada a servicios</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-8 mb-12">
                <!-- Plataforma Web Principal -->
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-95'" class="transition-all duration-500">
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 h-full">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-16 h-16 rounded-xl flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-700 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Plataforma Web Principal</h3>
                        </div>
                        <p class="text-gray-600 mb-6">Debian 12 en Azure VM con Docker, Apache y PHP</p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-gray-700 hover:text-blue-600 transition-colors">
                                <span class="text-blue-600 mt-1 text-xl">→</span>
                                <span><strong>Interfaz Administrador:</strong> Gestión de usuarios, flota, sedes y reportes</span>
                            </li>
                            <li class="flex items-start gap-3 text-gray-700 hover:text-blue-600 transition-colors">
                                <span class="text-blue-600 mt-1 text-xl">→</span>
                                <span><strong>Interfaz Cliente:</strong> Registro, reservas, pagos y seguimiento</span>
                            </li>
                            <li class="flex items-start gap-3 text-gray-700 hover:text-blue-600 transition-colors">
                                <span class="text-blue-600 mt-1 text-xl">→</span>
                                <span><strong>Interfaz Conductor:</strong> Gestión de rutas y entregas</span>
                            </li>
                            <li class="flex items-start gap-3 text-gray-700 hover:text-blue-600 transition-colors">
                                <span class="text-blue-600 mt-1 text-xl">→</span>
                                <span><strong>Módulo Auth:</strong> Laravel Breeze + Spatie Permissions</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- API Gateway -->
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-95'" class="transition-all duration-500 delay-100">
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 h-full">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">API Gateway</h3>
                        </div>
                        <p class="text-gray-600 mb-6">Punto de integración central con Laravel Sanctum</p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-gray-700 hover:text-green-600 transition-colors">
                                <span class="text-green-600 mt-1 text-xl">→</span>
                                <span>Interoperabilidad entre módulos</span>
                            </li>
                            <li class="flex items-start gap-3 text-gray-700 hover:text-green-600 transition-colors">
                                <span class="text-green-600 mt-1 text-xl">→</span>
                                <span>Seguridad de comunicaciones</span>
                            </li>
                            <li class="flex items-start gap-3 text-gray-700 hover:text-green-600 transition-colors">
                                <span class="text-green-600 mt-1 text-xl">→</span>
                                <span>Flujo estructurado de datos</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Base de Datos -->
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-95'" class="transition-all duration-500 delay-200">
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 h-full">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-700 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Base de Datos MySQL</h3>
                        </div>
                        <p class="text-gray-600 mb-6">Gestión relacional con Eloquent ORM</p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-gray-700 hover:text-orange-600 transition-colors">
                                <span class="text-orange-600 mt-1 text-xl">→</span>
                                <span>Usuarios, reservas y vehículos</span>
                            </li>
                            <li class="flex items-start gap-3 text-gray-700 hover:text-orange-600 transition-colors">
                                <span class="text-orange-600 mt-1 text-xl">→</span>
                                <span>Transacciones y pagos</span>
                            </li>
                            <li class="flex items-start gap-3 text-gray-700 hover:text-orange-600 transition-colors">
                                <span class="text-orange-600 mt-1 text-xl">→</span>
                                <span>Registros de actividad y telemetría</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Servicios IoT -->
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-95'" class="transition-all duration-500 delay-300">
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 h-full">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Telemetría GPS</h3>
                        </div>
                        <p class="text-gray-600 mb-6">Mosquitto MQTT para monitoreo IoT en tiempo real</p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-gray-700 hover:text-purple-600 transition-colors">
                                <span class="text-purple-600 mt-1 text-xl">→</span>
                                <span><strong>Publisher:</strong> Genera datos de telemetría GPS</span>
                            </li>
                            <li class="flex items-start gap-3 text-gray-700 hover:text-purple-600 transition-colors">
                                <span class="text-purple-600 mt-1 text-xl">→</span>
                                <span><strong>Subscriber:</strong> Envía datos al controlador</span>
                            </li>
                            <li class="flex items-start gap-3 text-gray-700 hover:text-purple-600 transition-colors">
                                <span class="text-purple-600 mt-1 text-xl">→</span>
                                <span>Rastreo de vehículos y domicilios</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Pasarela de Pago -->
            <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-95'" class="transition-all duration-500 delay-400">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-8 text-white shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-3">Pasarela de Pago: MercadoPago</h3>
                            <p class="text-blue-100 text-lg">Procesamiento seguro de transacciones para reservas, pagos por uso y servicios de domicilio</p>
                        </div>
                        <svg class="w-20 h-20 text-blue-200 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stack Tecnológico -->
    <section id="stack" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-16 text-center" x-data="{ show: false }" x-intersect="show = true">
                <div :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" class="transition-all duration-700">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Stack Tecnológico</h2>
                    <div class="h-1 w-20 rounded-full bg-blue-600 mx-auto"></div>
                </div>
            </div>

            <div class="grid md:grid-cols-4 gap-6 mb-12">
                <!-- Backend -->
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-500">
                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-8 border-2 border-red-200 hover:border-red-400 hover:shadow-xl transition-all duration-300 hover:-translate-y-2 h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-3 h-3 bg-red-600 rounded-full animate-pulse"></div>
                            <h3 class="text-xl font-bold text-gray-900">Backend</h3>
                        </div>
                        <ul class="space-y-3 text-sm text-gray-700">
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-red-600">•</span> Laravel 12+ (PHP 8.2+)
                            </li>
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-red-600">•</span> Eloquent ORM
                            </li>
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-red-600">•</span> MySQL 8.0+
                            </li>
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-red-600">•</span> Spatie Permissions
                            </li>
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-red-600">•</span> Mosquitto (MQTT)
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Frontend -->
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-500 delay-100">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 border-2 border-blue-200 hover:border-blue-400 hover:shadow-xl transition-all duration-300 hover:-translate-y-2 h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-3 h-3 bg-blue-600 rounded-full animate-pulse"></div>
                            <h3 class="text-xl font-bold text-gray-900">Frontend</h3>
                        </div>
                        <ul class="space-y-3 text-sm text-gray-700">
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-blue-600">•</span> Blade Templates
                            </li>
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-blue-600">•</span> Tailwind CSS v3
                            </li>
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-blue-600">•</span> Alpine.js
                            </li>
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-blue-600">•</span> Livewire
                            </li>
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-blue-600">•</span> Flux UI Components
                            </li>
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-blue-600">•</span> Chart.js
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Integraciones -->
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-500 delay-200">
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8 border-2 border-green-200 hover:border-green-400 hover:shadow-xl transition-all duration-300 hover:-translate-y-2 h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-3 h-3 bg-green-600 rounded-full animate-pulse"></div>
                            <h3 class="text-xl font-bold text-gray-900">Integraciones</h3>
                        </div>
                        <ul class="space-y-3 text-sm text-gray-700">
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-green-600">•</span> Leaflet.js (Mapas)
                            </li>
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-green-600">•</span> MercadoPago
                            </li>
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-green-600">•</span> Laravel Mail
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Infraestructura -->
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-500 delay-300">
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-8 border-2 border-purple-200 hover:border-purple-400 hover:shadow-xl transition-all duration-300 hover:-translate-y-2 h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-3 h-3 bg-purple-600 rounded-full animate-pulse"></div>
                            <h3 class="text-xl font-bold text-gray-900">Infraestructura</h3>
                        </div>
                        <ul class="space-y-3 text-sm text-gray-700">
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-purple-600">•</span> Debian 12 (Azure VM)
                            </li>
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-purple-600">•</span> Apache + PHP
                            </li>
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-purple-600">•</span> Docker
                            </li>
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-purple-600">•</span> Git & GitHub
                            </li>
                            <li class="flex items-center gap-2 hover:translate-x-2 transition-transform">
                                <span class="text-purple-600">•</span> GitHub Actions CI/CD
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Herramientas -->
            <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-95'" class="transition-all duration-700">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-8 border border-gray-200 hover:shadow-lg transition-all duration-300">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Herramientas de Desarrollo
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="flex items-center gap-2 text-sm text-gray-700 bg-white rounded-lg p-3 hover:shadow-md transition-all hover:scale-105">
                            <span class="text-blue-600 text-lg">→</span> Composer (PHP)
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700 bg-white rounded-lg p-3 hover:shadow-md transition-all hover:scale-105">
                            <span class="text-blue-600 text-lg">→</span> pnpm (JavaScript)
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700 bg-white rounded-lg p-3 hover:shadow-md transition-all hover:scale-105">
                            <span class="text-blue-600 text-lg">→</span> Postman (API Testing)
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700 bg-white rounded-lg p-3 hover:shadow-md transition-all hover:scale-105">
                            <span class="text-blue-600 text-lg">→</span> Laravel Sanctum
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Base de Datos -->
    <section id="base-datos" class="py-20 bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-16 text-center" x-data="{ show: false }" x-intersect="show = true">
                <div :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" class="transition-all duration-700">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Estructura de Base de Datos</h2>
                    <div class="h-1 w-20 rounded-full bg-blue-600 mx-auto mb-4"></div>
                    <p class="text-gray-600 text-lg">MySQL 8.0+ con arquitectura relacional y Eloquent ORM</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-8 mb-12">
                <!-- Migraciones -->
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-8'" class="transition-all duration-700">
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 hover:shadow-2xl transition-all duration-300 h-full">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                            </svg>
                            Migraciones (15 tablas)
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors">
                                <span class="text-gray-700 font-medium">users</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">1.5k</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors">
                                <span class="text-gray-700 font-medium">branches</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">914b</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors">
                                <span class="text-gray-700 font-medium">vehicles</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">1.6k</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors">
                                <span class="text-gray-700 font-medium">driver_profiles</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">808b</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors">
                                <span class="text-gray-700 font-medium">reservations</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">3.4k</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors">
                                <span class="text-gray-700 font-medium">payments</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">1.6k</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors">
                                <span class="text-gray-700 font-medium">telemetrias</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">2.3k</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors">
                                <span class="text-gray-700 font-medium">deliveries</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">1.4k</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modelos Eloquent -->
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-8'" class="transition-all duration-700">
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 hover:shadow-2xl transition-all duration-300 h-full">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                            <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                            </svg>
                            Modelos Eloquent (13 modelos)
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                                <span class="text-gray-700 font-medium">User.php</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">1.3k</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                                <span class="text-gray-700 font-medium">Branch.php</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">1.4k</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                                <span class="text-gray-700 font-medium">Vehicle.php</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">1.0k</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                                <span class="text-gray-700 font-medium">DriverProfile.php</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">725b</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                                <span class="text-gray-700 font-medium">Reservation.php</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">1.7k</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                                <span class="text-gray-700 font-medium">Payment.php</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">876b</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                                <span class="text-gray-700 font-medium">Telemetria.php</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">2.6k</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                                <span class="text-gray-700 font-medium">Delivery.php</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">1.0k</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Relaciones -->
            <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-95'" class="transition-all duration-700">
                <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-2xl p-8 text-white shadow-xl hover:shadow-2xl transition-all duration-300">
                    <h3 class="text-2xl font-bold mb-6">Relaciones entre Entidades</h3>
                    <div class="grid md:grid-cols-3 gap-4 text-sm">
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 hover:bg-white/20 transition-all hover:scale-105">
                            <strong class="block mb-2">User → Reservations</strong>
                            <p class="text-blue-100">hasMany (1:N)</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 hover:bg-white/20 transition-all hover:scale-105">
                            <strong class="block mb-2">Vehicle → Reservations</strong>
                            <p class="text-blue-100">hasMany (1:N)</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 hover:bg-white/20 transition-all hover:scale-105">
                            <strong class="block mb-2">Branch → Vehicles</strong>
                            <p class="text-blue-100">hasMany (1:N)</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 hover:bg-white/20 transition-all hover:scale-105">
                            <strong class="block mb-2">Driver → Deliveries</strong>
                            <p class="text-blue-100">hasMany (1:N)</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 hover:bg-white/20 transition-all hover:scale-105">
                            <strong class="block mb-2">Reservation → Payment</strong>
                            <p class="text-blue-100">hasOne (1:1)</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 hover:bg-white/20 transition-all hover:scale-105">
                            <strong class="block mb-2">Vehicle → Telemetria</strong>
                            <p class="text-blue-100">hasMany (1:N)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Funcionalidades Principales -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-16 text-center" x-data="{ show: false }" x-intersect="show = true">
                <div :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" class="transition-all duration-700">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Funcionalidades Principales</h2>
                    <div class="h-1 w-20 rounded-full bg-blue-600 mx-auto"></div>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Sistema Multi-Rol -->
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-90'" class="transition-all duration-500">
                    <div class="bg-white rounded-2xl p-8 shadow-lg border-2 border-gray-200 hover:border-blue-500 hover:shadow-2xl hover:-translate-y-3 transition-all duration-300 h-full group">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Sistema Multi-Rol</h3>
                        <p class="text-sm text-gray-600 mb-4">3 roles con permisos granulares mediante Spatie Permissions</p>
                        <ul class="space-y-2 text-xs text-gray-600 mb-6">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-blue-600 rounded-full"></span>
                                <strong>Usuario:</strong> Registro, reservas y pagos
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-blue-600 rounded-full"></span>
                                <strong>Conductor:</strong> Gestión de entregas
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-blue-600 rounded-full"></span>
                                <strong>Administrador:</strong> Control total
                            </li>
                        </ul>
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 font-medium group-hover:translate-x-2 transition-transform">
                            Ver usuarios →
                        </a>
                    </div>
                </div>

                <!-- Gestión de Vehículos -->
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-90'" class="transition-all duration-500 delay-100">
                    <div class="bg-white rounded-2xl p-8 shadow-lg border-2 border-gray-200 hover:border-green-500 hover:shadow-2xl hover:-translate-y-3 transition-all duration-300 h-full group">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Gestión de Vehículos</h3>
                        <p class="text-sm text-gray-600 mb-4">Control completo de flota y disponibilidad en tiempo real</p>
                        <ul class="space-y-2 text-xs text-gray-600 mb-6">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span>
                                Motos, bicicletas y patinetas
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span>
                                Estado y mantenimiento
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span>
                                Asignación por sede
                            </li>
                        </ul>
                        <a href="{{ route('admin.vehicles.index') }}" class="inline-flex items-center text-sm text-green-600 hover:text-green-700 font-medium group-hover:translate-x-2 transition-transform">
                            Ver vehículos →
                        </a>
                    </div>
                </div>

                <!-- Sistema de Reservas -->
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-90'" class="transition-all duration-500 delay-200">
                    <div class="bg-white rounded-2xl p-8 shadow-lg border-2 border-gray-200 hover:border-purple-500 hover:shadow-2xl hover:-translate-y-3 transition-all duration-300 h-full group">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Sistema de Reservas</h3>
                        <p class="text-sm text-gray-600 mb-4">Reserva anticipada con verificación de disponibilidad</p>
                        <ul class="space-y-2 text-xs text-gray-600 mb-6">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-purple-600 rounded-full"></span>
                                Reserva en sede o delivery
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-purple-600 rounded-full"></span>
                                Confirmación por email
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-purple-600 rounded-full"></span>
                                Integración MercadoPago
                            </li>
                        </ul>
                        <a href="{{ route('admin.reservations.index') }}" class="inline-flex items-center text-sm text-purple-600 hover:text-purple-700 font-medium group-hover:translate-x-2 transition-transform">
                            Ver reservas →
                        </a>
                    </div>
                </div>

                <!-- Domicilios -->
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-90'" class="transition-all duration-500 delay-300">
                    <div class="bg-white rounded-2xl p-8 shadow-lg border-2 border-gray-200 hover:border-orange-500 hover:shadow-2xl hover:-translate-y-3 transition-all duration-300 h-full group">
                        <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Servicio de Domicilios</h3>
                        <p class="text-sm text-gray-600 mb-4">Mensajería urbana con conductores verificados</p>
                        <ul class="space-y-2 text-xs text-gray-600 mb-6">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-orange-600 rounded-full"></span>
                                Asignación automática
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-orange-600 rounded-full"></span>
                                Cálculo por distancia
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-orange-600 rounded-full"></span>
                                Rastreo GPS en vivo
                            </li>
                        </ul>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm text-orange-600 hover:text-orange-700 font-medium group-hover:translate-x-2 transition-transform">
                            Crear domicilio →
                        </a>
                    </div>
                </div>

                <!-- Telemetría GPS -->
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-90'" class="transition-all duration-500 delay-400">
                    <div class="bg-white rounded-2xl p-8 shadow-lg border-2 border-gray-200 hover:border-red-500 hover:shadow-2xl hover:-translate-y-3 transition-all duration-300 h-full group">
                        <div class="w-16 h-16 bg-gradient-to-br from-red-400 to-red-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Telemetría en Tiempo Real</h3>
                        <p class="text-sm text-gray-600 mb-4">Monitoreo IoT con protocolo MQTT (Mosquitto)</p>
                        <ul class="space-y-2 text-xs text-gray-600 mb-6">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-red-600 rounded-full"></span>
                                Publisher: Simula GPS
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-red-600 rounded-full"></span>
                                Subscriber: Almacena datos
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-red-600 rounded-full"></span>
                                Visualización Leaflet.js
                            </li>
                        </ul>
                        <a href="{{ route('admin.map') }}" class="inline-flex items-center text-sm text-red-600 hover:text-red-700 font-medium group-hover:translate-x-2 transition-transform">
                            Ver mapa GPS →
                        </a>
                    </div>
                </div>

                <!-- Reportes y Analytics -->
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-90'" class="transition-all duration-500 delay-500">
                    <div class="bg-white rounded-2xl p-8 shadow-lg border-2 border-gray-200 hover:border-indigo-500 hover:shadow-2xl hover:-translate-y-3 transition-all duration-300 h-full group">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Reportes y Analytics</h3>
                        <p class="text-sm text-gray-600 mb-4">Dashboards interactivos con Chart.js</p>
                        <ul class="space-y-2 text-xs text-gray-600 mb-6">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full"></span>
                                Métricas de uso
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full"></span>
                                Reportes financieros
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full"></span>
                                Exportación Excel/PDF
                            </li>
                        </ul>
                        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-700 font-medium group-hover:translate-x-2 transition-transform">
                            Ver reportes →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Equipo -->
    <section id="equipo" class="py-20 bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-16 text-center" x-data="{ show: false }" x-intersect="show = true">
                <div :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" class="transition-all duration-700">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Equipo de Desarrollo</h2>
                    <div class="h-1 w-20 rounded-full bg-blue-600 mx-auto mb-4"></div>
                    <p class="text-gray-600 text-lg">Proyecto Final - Desarrollo y Arquitectura Backend</p>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8 mb-12">
                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-90'" class="transition-all duration-500">
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 text-center hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full mx-auto mb-6 flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
                            <span class="text-white font-bold text-3xl">MP</span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-xl mb-2">Maria Paula Saavedra Martinez</h3>
                        <p class="text-sm text-gray-600">Desarrolladora Backend</p>
                    </div>
                </div>

                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-90'" class="transition-all duration-500 delay-100">
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 text-center hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                        <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-green-700 rounded-full mx-auto mb-6 flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
                            <span class="text-white font-bold text-3xl">SC</span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-xl mb-2">Santiago Cardona Prada</h3>
                        <p class="text-sm text-gray-600">Desarrollador Backend</p>
                    </div>
                </div>

                <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-90'" class="transition-all duration-500 delay-200">
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 text-center hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                        <div class="w-24 h-24 bg-gradient-to-br from-purple-500 to-purple-700 rounded-full mx-auto mb-6 flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
                            <span class="text-white font-bold text-3xl">SA</span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-xl mb-2">Sergio Alejandro Amaya Corzo</h3>
                        <p class="text-sm text-gray-600">Desarrollador Backend</p>
                    </div>
                </div>
            </div>

            <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-95'" class="transition-all duration-700 mb-8">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-10 text-white text-center shadow-xl hover:shadow-2xl transition-all duration-300">
                    <h3 class="text-3xl font-bold mb-3">Docente: Mg. Fabián Suárez</h3>
                    <p class="text-blue-100 text-lg mb-2">Desarrollo y Arquitectura Backend</p>
                    <p class="text-sm text-blue-200">Universidad Autónoma de Bucaramanga - UNAB 2025</p>
                </div>
            </div>

            <!-- Agradecimientos -->
            <div x-data="{ show: false }" x-intersect="show = true" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-95'" class="transition-all duration-700">
                <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">Agradecimientos</h3>
                    <p class="text-gray-700 text-center max-w-3xl mx-auto leading-relaxed">
                        Queremos agradecer a Dios todo poderoso, a nuestro Señor y Salvador Jesucristo, 
                        y a nuestro profesor y mentor Mg. Fabián Suárez. Este proyecto fue posible gracias 
                        a la ayuda y guía brindada por ellos.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                            <span class="text-white font-bold text-lg">B</span>
                        </div>
                        <h3 class="font-bold text-xl">BgaGO</h3>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">Sistema de gestión de movilidad urbana inteligente para el Área Metropolitana de Bucaramanga</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-lg">Plataforma</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-white transition-colors">Dashboard</a></li>
                        <li><a href="{{ route('catalog.index') }}" class="hover:text-white transition-colors">Catálogo</a></li>
                        <li><a href="{{ route('admin.map') }}" class="hover:text-white transition-colors">Mapa GPS</a></li>
                        <li><a href="{{ route('admin.monitoring') }}" class="hover:text-white transition-colors">Monitoreo</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-lg">Administración</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="{{ route('admin.users.index') }}" class="hover:text-white transition-colors">Usuarios</a></li>
                        <li><a href="{{ route('admin.drivers.index') }}" class="hover:text-white transition-colors">Conductores</a></li>
                        <li><a href="{{ route('admin.vehicles.index') }}" class="hover:text-white transition-colors">Vehículos</a></li>
                        <li><a href="{{ route('admin.reservations.index') }}" class="hover:text-white transition-colors">Reservas</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-lg">Recursos</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="https://github.com/Paulasaah/BgaGO-Transportes" target="_blank" class="hover:text-white transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                            </svg>
                            GitHub
                        </a></li>
                        <li><a href="{{ route('admin.reports.index') }}" class="hover:text-white transition-colors">Reportes</a></li>
                        <li><a href="#proyecto" class="hover:text-white transition-colors">Documentación</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-400">
                <p class="mb-2">&copy; 2025 BgaGO Transportes - Universidad Autónoma de Bucaramanga</p>
                <p>Proyecto Final - Desarrollo y Arquitectura Backend</p>
            </div>
        </div>
    </footer>

</body>
</html>