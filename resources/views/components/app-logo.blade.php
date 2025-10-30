@props(['showName' => true])

<div {{ $attributes->merge(['class' => 'flex items-center gap-2 group']) }}>
    <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 transition-all duration-300 group-hover:shadow-lg group-hover:shadow-blue-500/50">
        <x-app-logo-icon class="size-5 fill-current text-white transition-transform duration-300 group-hover:scale-110" />
    </div>
    
    @if($showName)
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-none font-semibold transition-colors duration-300 group-hover:text-blue-600 dark:group-hover:text-blue-400">
            BgaGO
        </span>
    </div>
    @endif
</div>