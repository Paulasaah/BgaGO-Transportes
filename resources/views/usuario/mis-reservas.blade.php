<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-white via-blue-50 to-blue-100 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-800 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl md:text-5xl font-bold text-zinc-900 dark:text-white mb-4">
                    Mis Reservas
                </h1>
                <p class="text-lg text-zinc-600 dark:text-zinc-400">
                    Gestiona y consulta el historial de tus reservas
                </p>
            </div>

            <!-- Filtros -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 p-6 mb-8">
                <div class="flex flex-wrap gap-3">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        Todas (3)
                    </button>
                    <button class="px-4 py-2 bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-lg font-medium hover:bg-zinc-300 dark:hover:bg-zinc-600 transition-colors">
                        Activas (1)
                    </button>
                    <button class="px-4 py-2 bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-lg font-medium hover:bg-zinc-300 dark:hover:bg-zinc-600 transition-colors">
                        Completadas (1)
                    </button>
                    <button class="px-4 py-2 bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-lg font-medium hover:bg-zinc-300 dark:hover:bg-zinc-600 transition-colors">
                        Canceladas (1)
                    </button>
                </div>
            </div>

            <!-- Lista de Reservas (Datos Simulados) -->
            <div class="space-y-6">

                <!-- Reserva Activa -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                            <!-- Info del Vehículo -->
                            <div class="flex items-start gap-4 flex-1">
                                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-950/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Bicicleta Eléctrica</h3>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400">
                                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-1.5 animate-pulse"></span>
                                            Activa
                                        </span>
                                    </div>
                                    <div class="grid sm:grid-cols-2 gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ now()->addDays(2)->format('d/m/Y') }} - 10:00</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>3 horas</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            <span>Cabecera del Llano</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-semibold">$15.000</span>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">ID: RES-ABC123</p>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="flex flex-col gap-2 sm:flex-row md:flex-col">
                                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-sm whitespace-nowrap">
                                    Ver Detalle
                                </button>
                                <button class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-950/30 dark:hover:bg-red-950/50 dark:text-red-400 rounded-lg font-medium transition-colors text-sm whitespace-nowrap">
                                    Cancelar Reserva
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reserva Completada -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden hover:shadow-xl transition-shadow opacity-75">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                            <!-- Info del Vehículo -->
                            <div class="flex items-start gap-4 flex-1">
                                <div class="w-16 h-16 bg-purple-100 dark:bg-purple-950/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Patines en Línea</h3>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-zinc-100 text-zinc-700 dark:bg-zinc-900 dark:text-zinc-400">
                                            Completada
                                        </span>
                                    </div>
                                    <div class="grid sm:grid-cols-2 gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ now()->subDays(5)->format('d/m/Y') }} - 14:00</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>2 horas</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            <span>Floridablanca</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-semibold">$8.000</span>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">ID: RES-XYZ789</p>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="flex flex-col gap-2 sm:flex-row md:flex-col">
                                <button class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-900 dark:text-white rounded-lg font-medium transition-colors text-sm whitespace-nowrap">
                                    Ver Detalle
                                </button>
                                <a href="{{ route('catalogo') }}" class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-950/30 dark:hover:bg-blue-950/50 dark:text-blue-400 rounded-lg font-medium transition-colors text-sm whitespace-nowrap text-center">
                                    Reservar de Nuevo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reserva Cancelada -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden hover:shadow-xl transition-shadow opacity-60">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                            <!-- Info del Vehículo -->
                            <div class="flex items-start gap-4 flex-1">
                                <div class="w-16 h-16 bg-teal-100 dark:bg-teal-950/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-8 h-8 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Patineta Eléctrica</h3>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-400">
                                            Cancelada
                                        </span>
                                    </div>
                                    <div class="grid sm:grid-cols-2 gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="line-through">{{ now()->subDays(1)->format('d/m/Y') }} - 16:00</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>1 hora</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            <span>Piedecuesta</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-semibold line-through">$3.500</span>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-xs text-red-600 dark:text-red-400">Motivo: Cancelada por el usuario</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">ID: RES-DEF456</p>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="flex flex-col gap-2 sm:flex-row md:flex-col">
                                <button class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-900 dark:text-white rounded-lg font-medium transition-colors text-sm whitespace-nowrap">
                                    Ver Detalle
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- CTA para nueva reserva -->
            <div class="mt-12 text-center bg-gradient-to-r from-blue-50 to-blue-50 dark:from-blue-950/30 dark:to-blue-950/30 rounded-2xl p-12 border border-blue-200 dark:border-blue-800">
                <h3 class="text-3xl font-bold text-zinc-900 dark:text-white mb-4">
                    ¿Listo para tu próxima aventura?
                </h3>
                <p class="text-lg text-zinc-600 dark:text-zinc-400 mb-8 max-w-2xl mx-auto">
                    Reserva tu próximo vehículo ecológico y disfruta de la ciudad de una manera diferente
                </p>
                <a href="{{ route('catalogo') }}" class="inline-flex items-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-lg transition-all hover:scale-105 shadow-lg">
                    Explorar Vehículos
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>

        </div>
    </div>
</x-layouts.app>
