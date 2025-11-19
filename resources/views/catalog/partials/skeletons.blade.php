<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @for($i=0; $i<6; $i++)
        <div class="rounded-2xl overflow-hidden ring-1 ring-zinc-200 dark:ring-white/10 bg-white/50 dark:bg-white/5 backdrop-blur animate-pulse">
            <div class="aspect-video bg-zinc-200/60 dark:bg-zinc-800/40"></div>
            <div class="p-6 space-y-3">
                <div class="h-5 bg-zinc-200/80 dark:bg-zinc-700/60 rounded"></div>
                <div class="h-4 bg-zinc-200/80 dark:bg-zinc-700/60 rounded w-2/3"></div>
                <div class="h-10 bg-zinc-200/80 dark:bg-zinc-700/60 rounded"></div>
            </div>
        </div>
    @endfor
</div>