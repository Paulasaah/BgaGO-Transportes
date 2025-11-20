<div>
    @php($pago = $pago ?? session('pago_simulado'))
    @php($reservaDb = $reservaDb ?? session('reserva_db'))

    <div class="text-center mb-10">
        <div class="mx-auto w-24 h-24 rounded-full bg-blue-600/10 flex items-center justify-center">
            <svg class="w-14 h-14 text-blue-600">
                <circle cx="28" cy="28" r="26" fill="none" stroke="currentColor" stroke-width="4" class="opacity-20"></circle>
                <path d="M18 28l6 6 12-14" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" style="stroke-dasharray: 50; stroke-dashoffset: 50;">
                    <animate attributeName="stroke-dashoffset" from="50" to="0" dur="0.8s" fill="freeze"/>
                </path>
            </svg>
        </div>
        <h1 class="mt-6 text-4xl md:text-5xl font-bold text-zinc-900 dark:text-white">¡Pago Exitoso!</h1>
        <p class="mt-2 text-zinc-600 dark:text-zinc-400">Tu reserva ha sido confirmada correctamente</p>
    </div>

    <div class="rounded-3xl overflow-hidden shadow-lg">
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs uppercase opacity-90">ID de Transacción</div>
                    <div class="text-2xl font-bold tracking-wide">{{ $pago['transaction_id'] ?? 'PAY-XXXX' }}</div>
                </div>
                <div class="text-right">
                    <div class="text-xs opacity-90">Fecha</div>
                    <div class="text-sm font-semibold">{{ $pago['timestamp'] ?? now()->format('Y-m-d H:i') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-zinc-900 text-zinc-200 p-6">
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-sm bg-blue-600 text-white text-[10px] font-bold">i</span>
                        <span class="font-semibold">Información de Pago</span>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span>Método de pago</span><span class="font-semibold">{{ $pago['method'] ?? 'Tarjeta' }}</span></div>
                        <div class="flex justify-between"><span>Tarjeta terminada en</span><span class="font-semibold">****{{ $pago['last4'] ?? '0000' }}</span></div>
                        <div class="flex justify-between"><span>Estado</span><span class="inline-flex items-center gap-2"><span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span><span class="font-semibold">Aprobado</span></span></div>
                        <div class="flex justify-between"><span>Subtotal</span><span class="font-semibold">${{ number_format($reservaDb['monto'] ?? 0, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span>Descuento</span><span class="font-semibold text-emerald-400">-${{ number_format($reservaDb['descuento'] ?? 0, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span>Total Pagado</span><span class="text-blue-400 font-bold">${{ number_format($reservaDb['monto_final'] ?? ($pago['total'] ?? 0), 0, ',', '.') }}</span></div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-sm bg-blue-600 text-white text-[10px] font-bold">i</span>
                        <span class="font-semibold">Detalles de Reserva</span>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span>Reserva</span><span class="font-semibold">{{ $reservaDb['codigo'] ?? ($reservaDb['id'] ?? 'RES-XXXXXX') }}</span></div>
                        <div class="flex justify-between"><span>Vehículo</span><span class="font-semibold">{{ $reservaDb['vehiculo'] ?? 'N/A' }}</span></div>
                        <div class="flex justify-between"><span>Fecha de inicio</span><span class="font-semibold">{{ $reservaDb['fecha_inicio'] ?? '' }}</span></div>
                        <div class="flex justify-between"><span>Hora</span><span class="font-semibold">{{ $reservaDb['hora_inicio'] ?? '' }}</span></div>
                        <div class="flex justify-between"><span>Duración</span><span class="font-semibold">{{ $reservaDb['duracion_horas'] ?? 1 }} hora(s)</span></div>
                        <div class="flex justify-between"><span>Tipo</span><span class="font-semibold">{{ ($reservaDb['tipo_reserva'] ?? '') === 'domicilio' ? 'Entrega a domicilio' : 'Recoger en punto' }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 flex items-center justify-center gap-4">
        <a href="{{ route('catalog.reservations') }}" wire:navigate class="px-6 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition">Ver Reservas</a>
        <a href="{{ route('catalog.index') }}" wire:navigate class="px-6 py-3 rounded-lg ring-1 ring-zinc-300 dark:ring-white/10 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-white/10 font-semibold transition">Ir al Catálogo</a>
    </div>
</div>
