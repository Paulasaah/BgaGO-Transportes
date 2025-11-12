<div class="p-8">
    <form wire:submit="continuar" class="space-y-8">

        <!-- Selección de Vehículo -->
        <div>
            <label class="block text-lg font-semibold text-zinc-900 dark:text-white mb-4">
                Selecciona tu Vehículo *
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($vehiculos_disponibles as $key => $vehiculo)
                <label class="relative cursor-pointer group">
                    <input
                        type="radio"
                        wire:model.live="vehiculo_seleccionado"
                        value="{{ $key }}"
                        class="peer sr-only"
                    >
                    <div class="p-4 rounded-xl border-2 border-zinc-300 dark:border-zinc-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/30 transition-all hover:border-blue-400 hover:shadow-md">
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-1">{{ $vehiculo['nombre'] }}</h4>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ number_format($vehiculo['precio'], 0, ',', '.') }}</p>
                        <p class="text-xs text-zinc-500">por hora</p>
                    </div>
                </label>
                @endforeach
            </div>
            @error('vehiculo_seleccionado')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-8"></div>

        <!-- Tipo de Reserva -->
        <div>
            <label class="block text-lg font-semibold text-zinc-900 dark:text-white mb-4">
                Tipo de Reserva *
            </label>
            <div class="grid grid-cols-2 gap-4">
                <label class="relative cursor-pointer">
                    <input
                        type="radio"
                        wire:model.live="tipo_reserva"
                        value="punto"
                        class="peer sr-only"
                    >
                    <div class="p-6 rounded-xl border-2 border-zinc-300 dark:border-zinc-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/30 transition-all hover:border-blue-400 text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-zinc-600 dark:text-zinc-400 peer-checked:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-2">Recoger en Punto</h4>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Recoge el vehículo en una de nuestras sedes</p>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input
                        type="radio"
                        wire:model.live="tipo_reserva"
                        value="domicilio"
                        class="peer sr-only"
                    >
                    <div class="p-6 rounded-xl border-2 border-zinc-300 dark:border-zinc-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/30 transition-all hover:border-blue-400 text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-zinc-600 dark:text-zinc-400 peer-checked:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-2">Entrega a Domicilio</h4>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Te llevamos el vehículo donde estés (+$5.000)</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Punto de Recogida o Dirección -->
        @if($tipo_reserva === 'punto')
        <div>
            <label for="punto_recogida" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                Punto de Recogida *
            </label>
            <select
                id="punto_recogida"
                wire:model="punto_recogida"
                class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            >
                <option value="">Selecciona un punto</option>
                @foreach($puntos_disponibles as $key => $punto)
                <option value="{{ $key }}">{{ $punto }}</option>
                @endforeach
            </select>
            @error('punto_recogida')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        @else
        <div>
            <label for="direccion_recogida" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                Dirección de Entrega *
            </label>
            <input
                type="text"
                id="direccion_recogida"
                wire:model="direccion_recogida"
                placeholder="Ej: Calle 36 #10-20, Bucaramanga"
                class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            >
            @error('direccion_recogida')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        @endif

        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-8"></div>

        <!-- Fecha y Hora de Inicio -->
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label for="fecha_inicio" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                    Fecha de Inicio *
                </label>
                <input
                    type="date"
                    id="fecha_inicio"
                    wire:model.live="fecha_inicio"
                    min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                    class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                >
                @error('fecha_inicio')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="hora_inicio" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                    Hora de Inicio *
                </label>
                <input
                    type="time"
                    id="hora_inicio"
                    wire:model.live="hora_inicio"
                    class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                >
                @error('hora_inicio')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Duración -->
        <div>
            <label for="duracion_horas" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                Duración (horas) *
            </label>
            <div class="flex items-center gap-4">
                <input
                    type="range"
                    id="duracion_horas"
                    wire:model.live="duracion_horas"
                    min="1"
                    max="24"
                    class="flex-1 h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer dark:bg-zinc-700"
                >
                <div class="w-20 px-4 py-2 bg-blue-100 dark:bg-blue-950/50 rounded-lg text-center">
                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $duracion_horas }}</span>
                    <span class="text-xs text-zinc-600 dark:text-zinc-400 block">hora{{ $duracion_horas > 1 ? 's' : '' }}</span>
                </div>
            </div>
            @error('duracion_horas')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Notas Adicionales -->
        <div>
            <label for="notas_adicionales" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                Notas Adicionales (Opcional)
            </label>
            <textarea
                id="notas_adicionales"
                wire:model="notas_adicionales"
                rows="3"
                placeholder="¿Alguna instrucción especial o preferencia?"
                class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
            ></textarea>
        </div>

        <!-- Resumen de la Reserva -->
        @if($vehiculo_seleccionado && $fecha_inicio && $hora_inicio)
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-950/30 dark:to-blue-900/30 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
            <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">Resumen de la Reserva</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">Vehículo:</span>
                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $vehiculos_disponibles[$vehiculo_seleccionado]['nombre'] ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">Duración:</span>
                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $duracion_horas }} hora{{ $duracion_horas > 1 ? 's' : '' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">Precio por hora:</span>
                    <span class="font-semibold text-zinc-900 dark:text-white">${{ number_format($precio_hora, 0, ',', '.') }}</span>
                </div>
                @if($tipo_reserva === 'domicilio')
                <div class="flex justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">Entrega a domicilio:</span>
                    <span class="font-semibold text-zinc-900 dark:text-white">$5.000</span>
                </div>
                @endif
                @if($fecha_fin_estimada)
                <div class="flex justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">Devolución estimada:</span>
                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $fecha_fin_estimada }}</span>
                </div>
                @endif
                <div class="border-t border-blue-200 dark:border-blue-700 pt-3 mt-3"></div>
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-zinc-900 dark:text-white">Total:</span>
                    <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                        ${{ number_format($total_estimado + ($tipo_reserva === 'domicilio' ? 5000 : 0), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
        @endif

        <!-- Botones -->
        <div class="flex gap-4">
            <a href="{{ route('catalogo') }}"
               class="px-6 py-4 bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-900 dark:text-white rounded-lg font-semibold transition-colors text-center">
                Cancelar
            </a>
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="flex-1 px-6 py-4 bg-blue-600 hover:bg-blue-700 disabled:bg-zinc-400 text-white rounded-lg font-semibold text-lg transition-all hover:scale-[1.02] disabled:hover:scale-100 shadow-lg hover:shadow-xl disabled:cursor-not-allowed"
            >
                <span wire:loading.remove wire:target="continuar">
                    Continuar al Pago
                </span>
                <span wire:loading wire:target="continuar" class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Procesando...
                </span>
            </button>
        </div>

    </form>
</div>
