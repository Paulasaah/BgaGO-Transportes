<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-emerald-100 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-800 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('pago_exitoso'))
                @php
                    $pago = session('pago_exitoso');
                    $reserva = $pago['reserva'];
                @endphp

                <!-- Checkmark Animado -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-blue-100 dark:bg-blue-950/50 rounded-full mb-6 animate-bounce">
                        <svg class="w-12 h-12 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold text-zinc-900 dark:text-white mb-4">
                        ¡Pago Exitoso!
                    </h1>
                    <p class="text-xl text-zinc-600 dark:text-zinc-400">
                        Tu reserva ha sido confirmada correctamente
                    </p>
                </div>

                <!-- Card Principal -->
                <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden mb-6">

                    <!-- Header con ID de Pago -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-center">
                        <p class="text-blue-100 text-sm mb-2">ID de Transacción</p>
                        <p class="text-2xl font-bold text-white font-mono">{{ $pago['pago_id'] }}</p>
                        <p class="text-blue-100 text-sm mt-2">{{ \Carbon\Carbon::parse($pago['fecha'])->format('d/m/Y H:i') }}</p>
                    </div>

                    <!-- Resumen del Pago -->
                    <div class="p-8">
                        <div class="grid md:grid-cols-2 gap-8 mb-8">

                            <!-- Detalles del Pago -->
                            <div>
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Información de Pago
                                </h3>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">Método de pago</span>
                                        <span class="font-semibold text-zinc-900 dark:text-white">{{ $pago['metodo'] }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">Tarjeta terminada en</span>
                                        <span class="font-semibold text-zinc-900 dark:text-white">****{{ $pago['ultimos_digitos'] }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">Estado</span>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400">
                                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                            Aprobado
                                        </span>
                                    </div>
                                    <div class="flex justify-between py-2 pt-4">
                                        <span class="text-lg font-bold text-zinc-900 dark:text-white">Total Pagado</span>
                                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ number_format($pago['monto'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Detalles de la Reserva -->
                            <div>
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Detalles de Reserva
                                </h3>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">Vehículo</span>
                                        <span class="font-semibold text-zinc-900 dark:text-white capitalize">{{ str_replace('-', ' ', $reserva['vehiculo']) }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">Fecha de inicio</span>
                                        <span class="font-semibold text-zinc-900 dark:text-white">{{ $reserva['fecha_inicio'] }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">Hora</span>
                                        <span class="font-semibold text-zinc-900 dark:text-white">{{ $reserva['hora_inicio'] }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">Duración</span>
                                        <span class="font-semibold text-zinc-900 dark:text-white">{{ $reserva['duracion_horas'] }} hora(s)</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">Tipo</span>
                                        <span class="font-semibold text-zinc-900 dark:text-white">
                                            @if($reserva['tipo_reserva'] == 'punto')
                                                Recoger en punto
                                            @else
                                                Entrega a domicilio
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nota Importante -->
                        <div class="bg-blue-50 dark:bg-blue-950/30 border-l-4 border-blue-500 p-4 mb-6">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-sm text-blue-800 dark:text-blue-200">
                                    <p class="font-semibold mb-1">Confirmación enviada</p>
                                    <p>Hemos enviado un correo electrónico con los detalles de tu reserva y el recibo de pago.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="grid md:grid-cols-3 gap-4">
                            <a href="{{ route('mis-reservas') }}"
                               class="flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Mis Reservas
                            </a>

                            <button
                                onclick="window.print()"
                                class="flex items-center justify-center px-6 py-3 bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-900 dark:text-white rounded-lg font-medium transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Imprimir Recibo
                            </button>

                            <a href="{{ route('home') }}"
                               class="flex items-center justify-center px-6 py-3 bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-900 dark:text-white rounded-lg font-medium transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Volver al Inicio
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Próximos Pasos -->
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700 text-center">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-950/50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">1</span>
                        </div>
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-2">Revisa tu email</h4>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Recibirás un correo con todos los detalles</p>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700 text-center">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-950/50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">2</span>
                        </div>
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-2">Prepárate</h4>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Llega 10 minutos antes de tu hora de reserva</p>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700 text-center">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-950/50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">3</span>
                        </div>
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-2">Disfruta</h4>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">¡Listo para tu experiencia ecológica!</p>
                    </div>
                </div>

            @else
                <!-- Estado cuando no hay pago -->
                <div class="text-center py-20">
                    <svg class="w-24 h-24 mx-auto text-zinc-300 dark:text-zinc-700 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-3xl font-bold text-zinc-900 dark:text-white mb-4">No hay confirmación de pago</h2>
                    <p class="text-zinc-600 dark:text-zinc-400 mb-8">Parece que no has completado ningún pago recientemente</p>
                    <a href="{{ route('catalogo') }}"
                       class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Explorar Vehículos
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-layouts.app>
