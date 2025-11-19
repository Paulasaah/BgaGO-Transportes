@props([
    'name',
    'zone' => null,
    'address' => null,
    'phone' => null,
    'schedule' => null,
    'status' => 'open',
    'variant' => 'default'
])

<div class="group relative rounded-2xl p-6 bg-white text-zinc-900 ring-1 ring-zinc-200 dark:bg-zinc-900/60 dark:text-white dark:ring-white/10 shadow-xl transition-all duration-300 hover:-translate-y-1 hover:ring-blue-500/30">
    <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-[radial-gradient(ellipse_at_top_right,rgba(59,130,246,0.15),transparent_60%)]"></div>

    <div class="relative flex items-start justify-between">
        <div>
            <h3 class="text-xl font-semibold text-blue-600 dark:text-blue-400">{{ $name }}</h3>
            @if($zone)
                <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-300">{{ $zone }}</p>
            @endif
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $status === 'open' ? 'bg-blue-600 dark:bg-blue-500 text-white' : 'bg-zinc-600 text-white' }}">
            {{ $status === 'open' ? 'Abierto' : 'Cerrado' }}
        </span>
    </div>

    <div class="relative mt-4 space-y-3 text-sm text-zinc-700 dark:text-zinc-300">
        @if($address)
            <div class="flex items-start gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M12 22s8-4.5 8-11a8 8 0 1 0-16 0c0 6.5 8 11 8 11Z"/></svg>
                <span>{{ $address }}</span>
            </div>
        @endif
        @if($phone)
            <div class="flex items-start gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.09 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72 12.2 12.2 0 0 0 .65 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.17a2 2 0 0 1 2.11-.45 12.2 12.2 0 0 0 2.81.65A2 2 0 0 1 22 16.92Z"/></svg>
                <span>{{ $phone }}</span>
            </div>
        @endif
        @if($schedule)
            <div class="flex items-start gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>{{ $schedule }}</span>
            </div>
        @endif
    </div>
</div>