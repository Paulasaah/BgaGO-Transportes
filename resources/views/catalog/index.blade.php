@php
    $filters = request()->only(['tipo', 'ubicacion', 'precio_min', 'precio_max', 'orden']);
    $query = \App\Models\Vehicle::query()
        ->disponibles()
        ->visiblesEnCatalogo();

    if (!empty($filters['tipo'])) {
        $query->porTipo($filters['tipo']);
    }
    if (!empty($filters['ubicacion'])) {
        $u = $filters['ubicacion'];
        $query->whereHas('branch', function($q) use ($u) {
            $q->where('ciudad', 'like', "%{$u}%")
              ->orWhere('nombre', 'like', "%{$u}%");
        });
    }
    if (!empty($filters['precio_min'])) {
        $query->where('precio_hora', '>=', (float) $filters['precio_min']);
    }
    if (!empty($filters['precio_max'])) {
        $query->where('precio_hora', '<=', (float) $filters['precio_max']);
    }
    $orden = $filters['orden'] ?? 'relevancia';
    if ($orden === 'precio_asc') {
        $query->orderBy('precio_hora', 'asc');
    } elseif ($orden === 'precio_desc') {
        $query->orderBy('precio_hora', 'desc');
    } else {
        $query->latest();
    }

    $vehicles = $query->limit(12)->get();

    $mapVehicle = function($v) {
        $t = is_object($v->tipo) ? $v->tipo->value : $v->tipo;
        $iconType = $t === 'bicicleta' ? 'manual' : 'electric';
        return [
            'name' => trim(($v->marca.' '.$v->modelo)),
            'description' => ucfirst($t),
            'iconType' => $iconType,
            'price' => (int) $v->precio_hora,
            'specs' => array_filter([ucfirst($t), $v->year, $v->color]),
            'slug' => (string) $v->id,
        ];
    };

    $vehiclesData = $vehicles->map(fn($v) => $mapVehicle($v))->toArray();
    $popular = array_slice($vehiclesData, 0, 3);
    $recommendations = array_slice($vehiclesData, 3);
@endphp

<x-layouts.public>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="relative mt-6 rounded-3xl overflow-hidden ring-1 ring-zinc-200/70 dark:ring-white/10 bg-blue-600/10 dark:bg-blue-900/20">
            <div class="absolute inset-0" style="background-image: repeating-linear-gradient(135deg, rgba(30, 64, 175, 0.15) 0px, rgba(30, 64, 175, 0.15) 40px, transparent 40px, transparent 80px);"></div>
            <div class="relative z-10 grid lg:grid-cols-2 gap-6 p-8 sm:p-10">
                <div class="flex flex-col justify-center">
                    <h1 class="text-3xl sm:text-4xl font-bold text-zinc-900 dark:text-white">
                        La forma más fácil de alquilar tu bici o scooter
                    </h1>
                    <p class="mt-3 text-zinc-700 dark:text-zinc-300">
                        Disfruta de servicios de movilidad a bajo costo con instalaciones seguras y cómodas.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('catalog.index') }}" wire:navigate class="inline-flex items-center justify-center h-11 px-5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">Explorar</a>
                    </div>
                </div>
                <div class="relative h-48 sm:h-56 lg:h-64">
                    <img src="{{ asset('images/catalog/motocicleta.png') }}" alt="Moto" class="absolute right-28 top-1/2 -translate-y-1/2 w-56 sm:w-64 lg:w-72 object-contain" />
                    <img src="{{ asset('images/catalog/scooter_electrico.png') }}" alt="Scooter" class="absolute right-2 bottom-0 w-40 sm:w-48 lg:w-56 object-contain" />
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('catalog.index') }}" class="mt-6">
            <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white/70 dark:bg-white/5 backdrop-blur p-5 shadow-sm">
                <div class="grid gap-4 lg:grid-cols-6">
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Tipo</div>
                        <select name="tipo" class="w-full h-11 rounded-xl bg-zinc-50 dark:bg-white/10 text-zinc-800 dark:text-zinc-200 ring-1 ring-zinc-200 dark:ring-white/10 focus:ring-2 focus:ring-blue-600 transition">
                            <option value="">Todos</option>
                            <option value="bicicleta">Bicicleta</option>
                            <option value="scooter">Scooter</option>
                            <option value="patineta">Patineta</option>
                            <option value="moto">Moto</option>
                        </select>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Ubicación</div>
                        <input name="ubicacion" type="text" placeholder="Ciudad o sucursal" class="w-full h-11 rounded-xl ps-3 pe-3 bg-zinc-50 dark:bg-white/10 text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 dark:placeholder-zinc-500 ring-1 ring-zinc-200 dark:ring-white/10 focus:ring-2 focus:ring-blue-600 transition" />
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Precio mín</div>
                        <input name="precio_min" type="number" min="0" step="1000" placeholder="0" class="w-full h-11 rounded-xl ps-3 pe-3 bg-zinc-50 dark:bg-white/10 text-zinc-800 dark:text-zinc-200 ring-1 ring-zinc-200 dark:ring-white/10 focus:ring-2 focus:ring-blue-600 transition" />
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Precio máx</div>
                        <input name="precio_max" type="number" min="0" step="1000" placeholder="30000" class="w-full h-11 rounded-xl ps-3 pe-3 bg-zinc-50 dark:bg-white/10 text-zinc-800 dark:text-zinc-200 ring-1 ring-zinc-200 dark:ring-white/10 focus:ring-2 focus:ring-blue-600 transition" />
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Orden</div>
                        <select name="orden" class="w-full h-11 rounded-xl bg-zinc-50 dark:bg-white/10 text-zinc-800 dark:text-zinc-200 ring-1 ring-zinc-200 dark:ring-white/10 focus:ring-2 focus:ring-blue-600 transition">
                            <option value="relevancia">Relevancia</option>
                            <option value="precio_asc">Precio: menor a mayor</option>
                            <option value="precio_desc">Precio: mayor a menor</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-3">
                        <button type="submit" class="inline-flex items-center justify-center h-11 px-5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">Buscar</button>
                        <a href="{{ route('catalog.index') }}" class="inline-flex items-center justify-center h-11 px-4 rounded-lg ring-1 ring-zinc-300 dark:ring-white/10 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-white/10 transition-colors">Limpiar</a>
                    </div>
                </div>
            </div>
        </form>

        <div class="mt-8 flex items-center justify-between">
            <div class="text-base font-semibold text-zinc-900 dark:text-white">Popular</div>
            <a href="{{ route('catalog.index') }}" wire:navigate class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Ver más</a>
        </div>

        <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($popular as $v)
                <x-cards.vehicle-card
                    :name="$v['name']"
                    :description="$v['description']"
                    :iconType="$v['iconType']"
                    status="available"
                    :price="$v['price']"
                    :specs="$v['specs']"
                    :vehicleSlug="$v['slug']" />
            @endforeach
        </div>

        <div class="mt-10 text-base font-semibold text-zinc-900 dark:text-white">Recomendaciones</div>
        <div x-data="{ showAll: false }" class="mt-4">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach(array_slice($recommendations, 0, 3) as $v)
                    <x-cards.vehicle-card
                        :name="$v['name']"
                        :description="$v['description']"
                        :iconType="$v['iconType']"
                        status="available"
                        :price="$v['price']"
                        :specs="$v['specs']"
                        :vehicleSlug="$v['slug']" />
                @endforeach
                @foreach(array_slice($recommendations, 3) as $v)
                    <div x-show="showAll" x-transition>
                        <x-cards.vehicle-card
                            :name="$v['name']"
                            :description="$v['description']"
                            :iconType="$v['iconType']"
                            status="available"
                            :price="$v['price']"
                            :specs="$v['specs']"
                            :vehicleSlug="$v['slug']" />
                    </div>
                @endforeach
            </div>
            <div class="mt-8 flex items-center justify-center">
                <a href="#" @click.prevent="showAll = !showAll" class="inline-flex items-center justify-center h-11 px-5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                    <span x-text="showAll ? 'Mostrar menos' : 'Mostrar más'"></span>
                </a>
            </div>
        </div>
    </div>
</x-layouts.public>
