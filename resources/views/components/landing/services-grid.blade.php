@props([
    'title' => 'Nuestros Servicios',
    'subtitle' => 'Todo lo que necesitas para moverte por la ciudad',
    'services' => []
])

<section class="py-24 bg-white dark:bg-zinc-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-5xl font-bold text-zinc-900 dark:text-white">{{ $title }}</h2>
            <p class="mt-2 text-base text-zinc-600 dark:text-zinc-400">{{ $subtitle }}</p>
        </div>

        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($services as $s)
                <div class="group relative rounded-2xl p-8 bg-white ring-1 ring-zinc-200 text-zinc-900 dark:bg-zinc-900/60 dark:ring-white/10 dark:text-white shadow-xl transition-all duration-300 hover:-translate-y-1 hover:ring-blue-500/30">
                    <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-[radial-gradient(ellipse_at_top,rgba(59,130,246,0.10),transparent_60%)]"></div>
                    <div class="relative flex items-center justify-between mb-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-500 dark:text-white shadow-lg shadow-blue-500/30 group-hover:shadow-blue-500/50 transition-shadow">
                            @switch($s['iconType'])
                                @case('prestamo')
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><circle cx="10" cy="19" r="3"/><circle cx="18" cy="19" r="3"/><path d="M5 12l1-7h9l1 7"/><path d="M3 12h19"/></svg>
                                    @break
                                @case('domicilio')
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="M3.3 7L12 12l8.7-5"/></svg>
                                    @break
                                @case('gps')
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Z"/><circle cx="12" cy="9" r="2"/></svg>
                                    @break
                                @default
                                    <svg class="h-6 w-6" viewBox="0 0 24 24"></svg>
                            @endswitch
                        </div>
                        <span class="text-xs px-2 py-1 rounded bg-blue-50 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300">BgaGO</span>
                    </div>
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-white">{{ $s['title'] }}</h3>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ $s['description'] }}</p>
                    @if(!empty($s['features']))
                        <ul class="mt-4 space-y-2 text-sm text-zinc-700 dark:text-zinc-200">
                            @foreach($s['features'] as $f)
                                <li class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>{{ $f }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if(!empty($s['ctaText']))
                        <div class="mt-6">
                            <a href="{{ $s['ctaUrl'] ?? (isset($s['ctaRoute']) ? route($s['ctaRoute']) : '#') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors">
                                {{ $s['ctaText'] }}
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>