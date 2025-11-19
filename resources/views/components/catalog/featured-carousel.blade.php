@php
    $items = $items ?? [
        [
            'name' => 'Bicicleta Eléctrica',
            'price' => 12000,
            'specs' => ['Eléctrica', 'Autonomía 50km', '18kg'],
            'image' => asset('images/catalog/bicicleta_electrica.png'),
            'slug' => 'bicicleta-electrica',
        ],
        [
            'name' => 'Bicicleta Manual',
            'price' => 8000,
            'specs' => ['Manual', 'Ligera', '12kg'],
            'image' => asset('images/catalog/bicicleta_manual.png'),
            'slug' => 'bicicleta-manual',
        ],
        [
            'name' => 'Motocicleta',
            'price' => 25000,
            'specs' => ['150cc', 'Eficiente', 'Rápida'],
            'image' => asset('images/catalog/motocicleta.png'),
            'slug' => 'motocicleta',
        ],
        [
            'name' => 'Patineta',
            'price' => 6000,
            'specs' => ['Compacta', 'Portátil', 'Urbana'],
            'image' => asset('images/catalog/patineta.png'),
            'slug' => 'patineta',
        ],
        [
            'name' => 'Scooter Eléctrico',
            'price' => 14000,
            'specs' => ['Eléctrico', 'Autonomía 30km', '15kg'],
            'image' => asset('images/catalog/scooter_electrico.png'),
            'slug' => 'scooter-electrico',
        ],
    ];
@endphp

<section class="lg:col-span-2">
    <div class="flex items-center justify-between mb-3 px-1">
        <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Vehículos Destacados</h3>
        <a href="{{ route('catalog.index') }}" wire:navigate class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Ver todo</a>
    </div>
    <div class="relative rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white/70 dark:bg-white/5 backdrop-blur overflow-hidden">
        <div class="flex gap-6 py-4 px-4" style="min-width:max-content; animation: scroll-x 40s linear infinite; will-change: transform;">
            @foreach($items as $item)
                <a href="{{ route('catalog.index') }}" wire:navigate class="w-[220px] sm:w-[260px] md:w-[300px] shrink-0 rounded-xl overflow-hidden ring-1 ring-zinc-200 dark:ring-zinc-800 bg-white dark:bg-zinc-900 hover:shadow-md transition">
                    <div class="relative aspect-[4/3] bg-zinc-100 dark:bg-zinc-800">
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="absolute inset-0 w-full h-full object-contain p-4" />
                        <div class="absolute inset-x-0 bottom-0 px-3 py-2 bg-gradient-to-t from-black/50 to-transparent">
                            <div class="text-sm font-semibold text-white">{{ $item['name'] }}</div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div class="text-lg font-bold text-zinc-900 dark:text-white">${{ number_format($item['price'], 0, ',', '.') }}</div>
                            <span class="text-xs px-2 py-1 rounded-md bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Disponible</span>
                        </div>
                        <div class="mt-2 text-xs text-zinc-600 dark:text-zinc-400">
                            {{ implode(' • ', $item['specs']) }}
                        </div>
                    </div>
                </a>
            @endforeach
            @foreach($items as $item)
                <a href="{{ route('catalog.index') }}" wire:navigate class="w-[220px] sm:w-[260px] md:w-[300px] shrink-0 rounded-xl overflow-hidden ring-1 ring-zinc-200 dark:ring-zinc-800 bg-white dark:bg-zinc-900 hover:shadow-md transition">
                    <div class="relative aspect-[4/3] bg-zinc-100 dark:bg-zinc-800">
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="absolute inset-0 w-full h-full object-contain p-4" />
                        <div class="absolute inset-x-0 bottom-0 px-3 py-2 bg-gradient-to-t from-black/50 to-transparent">
                            <div class="text-sm font-semibold text-white">{{ $item['name'] }}</div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div class="text-lg font-bold text-zinc-900 dark:text-white">${{ number_format($item['price'], 0, ',', '.') }}</div>
                            <span class="text-xs px-2 py-1 rounded-md bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Disponible</span>
                        </div>
                        <div class="mt-2 text-xs text-zinc-600 dark:text-zinc-400">
                            {{ implode(' • ', $item['specs']) }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    <style>
        @keyframes scroll-x { from { transform: translateX(0); } to { transform: translateX(-50%); } }
    </style>
</section>