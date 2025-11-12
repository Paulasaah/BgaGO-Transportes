<div class="bg-white dark:bg-zinc-800 rounded-lg shadow border border-zinc-200 dark:border-zinc-700">
    <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center justify-between">
            <flux:heading size="lg">Alertas Activas</flux:heading>
            @if(count($alerts) > 0)
                <flux:badge variant="danger" size="lg">
                    {{ count($alerts) }}
                </flux:badge>
            @endif
        </div>
    </div>

    <div class="divide-y divide-zinc-200 dark:divide-zinc-700 max-h-80 overflow-y-auto">
        @forelse($alerts as $alert)
            <div class="p-3 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                <div class="flex items-start gap-2">
                    @php
                        $severityConfig = [
                            'error' => [
                                'color' => 'text-red-500', 
                                'bgColor' => 'bg-red-100 dark:bg-red-900/30',
                                'icon' => 'exclamation-circle'
                            ],
                            'warning' => [
                                'color' => 'text-orange-500', 
                                'bgColor' => 'bg-orange-100 dark:bg-orange-900/30',
                                'icon' => 'exclamation-triangle'
                            ],
                            'info' => [
                                'color' => 'text-blue-500', 
                                'bgColor' => 'bg-blue-100 dark:bg-blue-900/30',
                                'icon' => 'information-circle'
                            ],
                        ];
                        $config = $severityConfig[$alert['severidad']] ?? $severityConfig['info'];
                    @endphp
                    
                    <div class="size-8 rounded-full {{ $config['bgColor'] }} flex items-center justify-center flex-shrink-0">
                        @if($config['icon'] === 'exclamation-circle')
                            <flux:icon.exclamation-circle class="size-4 {{ $config['color'] }}" />
                        @elseif($config['icon'] === 'exclamation-triangle')
                            <flux:icon.exclamation-triangle class="size-4 {{ $config['color'] }}" />
                        @else
                            <flux:icon.information-circle class="size-4 {{ $config['color'] }}" />
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <flux:heading size="sm" class="text-sm">
                                {{ $alert['titulo'] }}
                            </flux:heading>
                            <flux:badge :variant="$this->getSeverityColor($alert['severidad'])" size="sm" class="flex-shrink-0">
                                {{ ucfirst($alert['severidad']) }}
                            </flux:badge>
                        </div>

                        <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-1 line-clamp-2">
                            {{ $alert['mensaje'] }}
                        </p>

                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $alert['timestamp'] }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-zinc-500 dark:text-zinc-400">
                <flux:icon.shield-check class="size-12 mx-auto mb-2 opacity-50" />
                <p class="text-sm">No hay alertas activas</p>
            </div>
        @endforelse
    </div>

    @if(count($alerts) > 3)
        <div class="p-3 border-t border-zinc-200 dark:border-zinc-700 text-center">
            <flux:button variant="ghost" size="sm" href="/admin/alerts">
                Ver todas las alertas
            </flux:button>
        </div>
    @endif
</div>