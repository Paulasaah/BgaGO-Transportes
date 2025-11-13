<x-layouts.public>
    <div class="py-24 min-h-screen bg-gradient-to-br from-white via-blue-50 to-blue-200 overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-bold text-blue-600 dark:text-blue mb-4">
                    Procesar Pago
                </h1>
                <p class="text-lg text-zinc-800 dark:text-zinc-600">
                    Completa tu reserva de forma segura
                </p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">

                <!-- Resumen de la Reserva (Sidebar) -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-700 p-6 sticky top-6">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Resumen de Reserva
                        </h2>

                        @if(session('reserva_temporal'))
                            @php
                                $reserva = session('reserva_temporal');
                            @endphp

                            <div class="space-y-4 text-sm">
                                <div class="pb-4 border-b border-zinc-200 dark:border-zinc-700">
                                    <p class="text-zinc-500 dark:text-zinc-400 mb-1">Vehículo</p>
                                    <p class="font-semibold text-zinc-900 dark:text-white capitalize">{{ str_replace('-', ' ', $reserva['vehiculo'] ?? 'N/A') }}</p>
                                </div>

                                <div class="pb-4 border-b border-zinc-200 dark:border-zinc-700">
                                    <p class="text-zinc-500 dark:text-zinc-400 mb-1">Tipo de Reserva</p>
                                    <p class="font-semibold text-zinc-900 dark:text-white">
                                        @if($reserva['tipo_reserva'] == 'punto')
                                            Recoger en Punto
                                        @else
                                            Entrega a Domicilio
                                        @endif
                                    </p>
                                </div>

                                <div class="pb-4 border-b border-zinc-200 dark:border-zinc-700">
                                    <p class="text-zinc-500 dark:text-zinc-400 mb-1">Fecha y Hora</p>
                                    <p class="font-semibold text-zinc-900 dark:text-white">{{ $reserva['fecha_inicio'] }} - {{ $reserva['hora_inicio'] }}</p>
                                </div>

                                <div class="pb-4 border-b border-zinc-200 dark:border-zinc-700">
                                    <p class="text-zinc-500 dark:text-zinc-400 mb-1">Duración</p>
                                    <p class="font-semibold text-zinc-900 dark:text-white">{{ $reserva['duracion_horas'] }} hora(s)</p>
                                </div>

                                <div class="pt-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-zinc-500 dark:text-zinc-400">Subtotal</span>
                                        <span class="font-semibold text-zinc-900 dark:text-white">${{ number_format($reserva['total'], 0, ',', '.') }}</span>
                                    </div>
                                    @if($reserva['tipo_reserva'] == 'domicilio')
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-zinc-500 dark:text-zinc-400">Entrega a domicilio</span>
                                        <span class="font-semibold text-zinc-900 dark:text-white">$5.000</span>
                                    </div>
                                    @endif
                                    <div class="flex justify-between items-center pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                        <span class="text-lg font-bold text-zinc-900 dark:text-white">Total a Pagar</span>
                                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                            ${{ number_format($reserva['total'] + ($reserva['tipo_reserva'] == 'domicilio' ? 5000 : 0), 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-16 h-16 mx-auto text-zinc-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-zinc-600 dark:text-zinc-400">No hay reserva activa</p>
                                <a href="{{ route('catalogo') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-700">
                                    Ver catálogo
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Formulario de Pago -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6">
                            <h2 class="text-2xl font-bold text-white flex items-center">
                                <svg class="w-7 h-7 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Información de Pago
                            </h2>
                            <p class="text-blue-50 mt-2">Todos los pagos son procesados de forma segura</p>
                        </div>

                        <div class="p-8">
                            @if(session('reserva_temporal'))
                                @livewire('procesar-pago')
                            @else
                                <div class="text-center py-12">
                                    <svg class="w-20 h-20 mx-auto text-zinc-300 dark:text-zinc-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-2">No hay reserva para pagar</h3>
                                    <p class="text-zinc-600 dark:text-zinc-400 mb-6">
                                        Primero debes crear una reserva antes de procesar el pago
                                    </p>
                                    <a href="{{ route('catalogo') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                        Ver Vehículos Disponibles
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Información de Seguridad -->
                    <div class="mt-6 grid md:grid-cols-3 gap-4">
                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700 text-center">
                            <svg class="w-8 h-8 mx-auto text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">Pago Seguro</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Encriptación SSL</p>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700 text-center">
                            <svg class="w-8 h-8 mx-auto text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">Protegido</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Datos protegidos</p>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700 text-center">
                            <svg class="w-8 h-8 mx-auto text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">Soporte 24/7</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Siempre disponible</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-layouts.public>
