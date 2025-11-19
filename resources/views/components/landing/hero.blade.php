{{--
    Componente: Hero Section
    Propósito: Hero con diseño EXACTO del home actual
    Ubicación: resources/views/components/landing/hero.blade.php

    Props:
    - title: Título HTML
    - subtitle: Subtítulo
    - primaryCta: ['text' => '', 'route' => '']
    - secondaryCta: ['text' => '', 'route' => ''] (opcional)
    - stats: Array de estadísticas (opcional)
--}}

@props([
    'title',
    'subtitle',
    'primaryCta' => null,
    'secondaryCta' => null,
    'stats' => null
])

<section class="relative min-h-[85vh] bg-gradient-to-b from-white via-white to-zinc-50 dark:from-black dark:via-black dark:to-zinc-900 overflow-hidden">
    <!-- Animación de fondo -->
    <div class="absolute inset-0">
        <div class="bg-[radial-gradient(ellipse_at_top_left,rgba(59,130,246,0.12),transparent_60%)] dark:bg-[radial-gradient(ellipse_at_top_left,rgba(59,130,246,0.15),transparent_60%)] w-full h-full"></div>
    </div>

    <!-- Contenedor principal -->
    <div class="relative z-10 max-w-[90rem] mx-auto px-2 sm:px-4 lg:px-6 py-20 grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-16 xl:gap-24 items-center">

        <!-- Texto izquierda -->
        <div class="space-y-6 md:pr-12 xl:pr-24">
            <h1 class="text-5xl md:text-7xl font-extrabold text-zinc-900 dark:text-white leading-tight">
                {!! $title !!}
            </h1>

            <p class="text-xl md:text-2xl text-zinc-700 dark:text-zinc-300">
                {{ $subtitle }}
            </p>

            <!-- CTAs -->
            @if($primaryCta || $secondaryCta)
                <div class="flex flex-col sm:flex-row gap-4 mt-8 md:ml-4 lg:ml-8">
                    @if($primaryCta)
                        <a href="{{ $primaryCta['url'] ?? (isset($primaryCta['route']) ? route($primaryCta['route']) : '#') }}"
                           class="inline-flex items-center justify-center px-8 py-4 bg-blue-600 text-white hover:bg-blue-700 dark:bg-white dark:text-black dark:hover:bg-zinc-200 rounded-lg font-semibold text-lg transition-colors">
                            <span>{{ $primaryCta['text'] }}</span>
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24"
                                 aria-hidden="true">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    @endif

                    @if($secondaryCta)
                        <a href="{{ $secondaryCta['url'] ?? (isset($secondaryCta['route']) ? route($secondaryCta['route']) : '#') }}"
                           class="inline-flex items-center justify-center px-8 py-4 text-zinc-900 border border-zinc-300 hover:bg-zinc-100 dark:text-white dark:border-white/20 dark:hover:bg-white/10 rounded-lg font-semibold text-lg transition-colors">
                            {{ $secondaryCta['text'] }}
                        </a>
                    @endif
                </div>
            @endif
            @if($stats)
                <div class="mt-10" x-data="{ total: {{ count($stats) }}, cardW: 260, gap: 20, containerW: 0, virtualIdx: {{ count($stats) }}, transition: true, measure(){ this.containerW = this.$refs.container.offsetWidth }, center(){ return (this.containerW - this.cardW)/2 }, next(){ this.transition = true; this.virtualIdx++; const max = this.total*2 - 1; if(this.virtualIdx > max){ setTimeout(()=>{ this.transition=false; this.virtualIdx = this.virtualIdx - this.total; }, 510); setTimeout(()=>{ this.transition=true; }, 520); } }, prev(){ this.transition = true; this.virtualIdx--; const min = this.total; if(this.virtualIdx < min){ setTimeout(()=>{ this.transition=false; this.virtualIdx = this.virtualIdx + this.total; }, 510); setTimeout(()=>{ this.transition=true; }, 520); } } }" x-init="measure(); window.addEventListener('resize', ()=>measure()); setInterval(()=>next(), 3500)">
                    <div class="relative">
                        <div x-ref="container" class="overflow-hidden w-full sm:max-w-[600px] md:max-w-[720px]">
                            <div class="flex" :class="transition ? 'transition-transform duration-500' : ''" :style="`gap: ${gap}px; transform: translateX(${ - (virtualIdx*(cardW+gap) - center()) }px)`">
                                @foreach(array_merge($stats, $stats, $stats) as $stat)
                                    <div class="shrink-0 w-[260px] rounded-2xl px-6 py-6 ring-1 ring-white/10 shadow-lg transform transition-all duration-300 text-center"
                                         x-bind:class="(virtualIdx % {{ count($stats) }}) === {{ ($loop->index % count($stats)) }} ? 'scale-105 opacity-100 bg-blue-500 text-white' : 'opacity-40 bg-blue-500/40 text-white'">
                                        <div class="text-lg font-semibold">{{ $stat['label'] }}</div>
                                        <div class="mt-2 text-2xl font-bold">{{ $stat['value'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="absolute inset-y-0 left-0 flex items-center">
                            <button @click="prev()" class="p-2 rounded-full bg-black/40 text-white hover:bg-black/60">‹</button>
                        </div>
                        <div class="absolute inset-y-0 right-0 flex items-center">
                            <button @click="next()" class="p-2 rounded-full bg-black/40 text-white hover:bg-black/60">›</button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Ilustración derecha: slider simple con Alpine -->
        <div class="relative" x-data="{ idx: 0, slides: ['{{ asset('images/hero/skater.png') }}','{{ asset('images/hero/bike.png') }}','{{ asset('images/hero/scooter.png') }}'] }" x-init="setInterval(()=>{ idx = (idx+1)%slides.length }, 7000)">

            <div class="w-full max-w-xl md:max-w-[680px] mx-auto md:mx-0 md:ml-auto flex items-center justify-center rounded-[36px] bg-zinc-100 ring-1 ring-zinc-200 dark:bg-blue-600 dark:ring-black md:h-[560px] h-[440px] relative overflow-hidden">
                <template x-for="(src, i) in slides" :key="i">
                    <img x-show="idx === i"
                         x-transition:enter="transition-opacity ease-in-out duration-1000"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition-opacity ease-in-out duration-1000"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         :src="src"
                         :alt="['Patinadora','Ciclista','Scooter'][i]"
                         class="absolute inset-0 m-0 w-full h-full object-cover select-none"
                         loading="lazy" decoding="async"
                         draggable="false" />
                </template>
            </div>
        </div>
    </div>


    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-zinc-700 dark:text-white"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24"
             aria-hidden="true">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
    </div>
</section>
