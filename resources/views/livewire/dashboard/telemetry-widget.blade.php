<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6 flex flex-col h-full">
    <div class="flex items-center gap-3 mb-4">
        <div class="p-2 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg">
            <svg class="size-5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <div>
            <flux:heading size="lg">Telemetría</flux:heading>
            <flux:subheading class="text-xs">Estado de flota</flux:subheading>
        </div>
    </div>

    <div class="flex-1 flex flex-col gap-2.5">
        {{-- Batería Promedio --}}
        @php $batteryClasses = $this->getBatteryClasses(); @endphp
        <div class="flex-1 p-3 rounded-lg {{ $batteryClasses['bg'] }} border {{ $batteryClasses['border'] }} flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Batería</span>
                <svg class="size-4 {{ $batteryClasses['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                    {{ $stats['avg_battery'] }}%
                </div>
                @if($stats['low_battery_count'] > 0)
                    <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">
                        {{ $stats['low_battery_count'] }} críticas
                    </p>
                @endif
            </div>
        </div>

        {{-- Kilómetros Hoy --}}
        <div class="flex-1 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Km Hoy</span>
                <svg class="size-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ number_format($stats['total_km_today'], 0) }}
            </div>
        </div>

        {{-- Mantenimiento --}}
        <div class="flex-1 p-3 rounded-lg bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Mantenim.</span>
                <svg class="size-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $stats['maintenance_needed'] }}
            </div>
        </div>

        {{-- Vehículos Activos --}}
        <div class="flex-1 p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Activos</span>
                <svg class="size-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                    {{ $stats['active_vehicles'] }}/{{ $stats['total_vehicles'] }}
                </div>
                <p class="text-xs text-green-600 dark:text-green-400 mt-0.5">
                    {{ $stats['total_vehicles'] > 0 ? round(($stats['active_vehicles'] / $stats['total_vehicles']) * 100) : 0 }}% tasa
                </p>
            </div>
        </div>
    </div>

</div>
