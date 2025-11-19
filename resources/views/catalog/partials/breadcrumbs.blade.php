<nav class="text-sm" aria-label="Breadcrumb">
    <ol class="flex items-center gap-2 text-zinc-600 dark:text-zinc-400">
        <li>
            <a href="{{ route('catalog.index') }}" wire:navigate class="hover:text-zinc-900 dark:hover:text-white">Catálogo</a>
        </li>
        @if(!empty($items))
            @foreach($items as $i)
                <li class="text-zinc-400">/</li>
                <li>
                    @if(!empty($i['href']))
                        <a href="{{ $i['href'] }}" wire:navigate class="hover:text-zinc-900 dark:hover:text-white">{{ $i['label'] }}</a>
                    @else
                        <span class="text-zinc-900 dark:text-white">{{ $i['label'] }}</span>
                    @endif
                </li>
            @endforeach
        @endif
</ol>
</nav>