{{--
    Vista: Reservar Vehículo
    Ruta: /reservar
    Ubicación: resources/views/usuario/reservar.blade.php

    Esta vista muestra el formulario de reserva usando Livewire
--}}

<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-white via-blue-50 to-blue-100 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-800 py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="text-center mb-8">
                <a href="{{ route('catalogo') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 mb-4 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Volver al Catálogo
                </a>
                <h1 class="text-4xl md:text-5xl font-bold text-zinc-900 dark:text-white mb-4">
                    Reserva tu Vehículo
                </h1>
                <p class="text-lg text-zinc-600 dark:text-zinc-400">
                    Completa los datos para realizar tu reserva
                </p>
            </div>

            {{-- Formulario de Reserva (Componente Livewire) --}}
            <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                @livewire('formulario-reserva')
            </div>

            {{-- Información Adicional --}}
            <div class="mt-8 grid md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700 text-center">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-950/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-zinc-900 dark:text-white mb-2">Confirmación Inmediata</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Recibe confirmación al instante por email</p>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700 text-center">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-950/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-zinc-900 dark:text-white mb-2">Pago Seguro</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Pasarela de pago cifrada y confiable</p>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700 text-center">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-950/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-zinc-900 dark:text-white mb-2">Soporte 24/7</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Estamos aquí para ayudarte siempre</p>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
