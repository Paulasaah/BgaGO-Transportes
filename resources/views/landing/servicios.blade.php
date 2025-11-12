<x-layouts.public>
    <section class="py-24 min-h-screen bg-gradient-to-br from-white via-blue-50 to-blue-200 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="text-center mb-16">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-500 dark:bg-blue-500 rounded-full mb-6">
                    <svg class="w-10 h-10 text-blue-100 dark:text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-blue-500 dark:text-blue-500 mb-4">
                    Servicio de Domicilios
                </h1>
                <p class="text-xl text-zinc-500 dark:text-zinc-500 max-w-2xl mx-auto">
                    Envía paquetes y documentos de forma rápida y segura. Mensajería local confiable en todo el Área Metropolitana.
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid md:grid-cols-3 gap-6 mb-16">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-950/50 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-2">Entregas Rápidas</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">Entregas en menos de 60 minutos dentro del área metropolitana</p>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-950/50 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-2">Conductores Verificados</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">Personal capacitado y verificado para tu tranquilidad</p>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-950/50 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-2">Seguro Incluido</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">Protección completa en cada envío que realizas</p>
                </div>
            </div>

            <!-- Formulario de Solicitud de Domicilio -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6">
                        <h2 class="text-2xl font-bold text-white">Solicitar Domicilio</h2>
                        <p class="text-blue-50 mt-1">Completa los datos para tu envío</p>
                    </div>

                    <div class="p-8">
                        @auth
                            @livewire('formulario-domicilio')
                        @else
                            <!-- Mensaje para usuarios no autenticados -->
                            <div class="text-center py-12">
                                <div class="inline-flex items-center justify-center w-20 h-20 bg-zinc-100 dark:bg-zinc-900 rounded-full mb-6">
                                    <svg class="w-10 h-10 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-zinc-900 dark:text-white mb-4">Inicia Sesión para Continuar</h3>
                                <p class="text-zinc-600 dark:text-zinc-400 mb-8 max-w-md mx-auto">
                                    Para solicitar un domicilio necesitas tener una cuenta. Es rápido y gratuito.
                                </p>
                                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                    <a href="{{ route('login') }}"
                                       class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                        Iniciar Sesión
                                    </a>
                                    <a href="{{ route('register') }}"
                                       class="inline-flex items-center justify-center px-6 py-3 bg-zinc-100 dark:bg-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-600 text-zinc-900 dark:text-white rounded-lg font-medium transition-colors">
                                        Crear Cuenta
                                    </a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="mt-16 grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Cobertura
                    </h3>
                    <ul class="space-y-2 text-zinc-600 dark:text-zinc-400">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            Bucaramanga
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            Floridablanca
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            Piedecuesta
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            Girón
                        </li>
                    </ul>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tarifas
                    </h3>
                    <ul class="space-y-2 text-zinc-600 dark:text-zinc-400">
                        <li class="flex justify-between">
                            <span>Hasta 2km</span>
                            <span class="font-semibold">$3.000</span>
                        </li>
                        <li class="flex justify-between">
                            <span>2km - 5km</span>
                            <span class="font-semibold">$5.000</span>
                        </li>
                        <li class="flex justify-between">
                            <span>5km - 10km</span>
                            <span class="font-semibold">$8.000</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Más de 10km</span>
                            <span class="font-semibold">$10.000+</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </section>
</x-layouts.public>
