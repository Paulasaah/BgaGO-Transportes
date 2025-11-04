<div class="bg-white dark:bg-zinc-800 rounded-lg shadow border border-zinc-200 dark:border-zinc-700">
    <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
        <flux:heading size="lg">Línea de Tiempo</flux:heading>
    </div>

    <div class="p-4 max-h-80 overflow-y-auto">
        <div class="relative">
            @if(count($events) > 0)
                {{-- Línea vertical --}}
                <div class="absolute left-3 top-2 bottom-2 w-0.5 bg-zinc-200 dark:bg-zinc-700"></div>

                <div class="space-y-3">
                    @foreach($events as $event)
                        <div class="relative pl-9">
                            @php
                                $colorConfig = [
                                    'blue' => 'bg-blue-500 ring-blue-100 dark:ring-blue-900/30',
                                    'green' => 'bg-green-500 ring-green-100 dark:ring-green-900/30',
                                    'gray' => 'bg-zinc-400 ring-zinc-100 dark:ring-zinc-700',
                                    'red' => 'bg-red-500 ring-red-100 dark:ring-red-900/30',
                                    'orange' => 'bg-orange-500 ring-orange-100 dark:ring-orange-900/30',
                                ];
                                $colorClass = $colorConfig[$event['color']] ?? $colorConfig['gray'];
                            @endphp
                            
                            {{-- Punto en la línea --}}
                            <div class="absolute left-0 top-1 size-6 rounded-full {{ $colorClass }} ring-4 flex items-center justify-center">
                                @if($event['icono'] === 'play')
                                    <flux:icon.play class="size-3 text-white" />
                                @elseif($event['icono'] === 'check')
                                    <flux:icon.check class="size-3 text-white" />
                                @elseif($event['icono'] === 'user')
                                    <flux:icon.user class="size-3 text-white" />
                                @elseif($event['icono'] === 'flag')
                                    <flux:icon.flag class="size-3 text-white" />
                                @else
                                    <flux:icon.information-circle class="size-3 text-white" />
                                @endif
                            </div>

                            <div class="bg-zinc-50 dark:bg-zinc-700/50 rounded-lg p-3">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <flux:subheading class="text-xs font-semibold">
                                        {{ $event['titulo'] }}
                                    </flux:subheading>
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($event['timestamp'])->format('H:i') }}
                                    </span>
                                </div>

                                <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                    {{ $event['descripcion'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-zinc-500 dark:text-zinc-400 py-8">
                    <flux:icon.clock class="size-12 mx-auto mb-2 opacity-50" />
                    <p class="text-sm">No hay eventos recientes</p>
                </div>
            @endif
        </div>
    </div>
</div>