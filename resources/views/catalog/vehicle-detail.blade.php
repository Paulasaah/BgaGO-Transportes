@php
    $vehicleId = request()->route('id');
    $vehicle = \App\Models\Vehicle::with('branch')->findOrFail($vehicleId);
    $tipo = is_object($vehicle->tipo) ? $vehicle->tipo->value : $vehicle->tipo;

    $typeImage = function ($t) {
        return match($t) {
            'bicicleta' => asset('images/catalog/bicicleta_manual.png'),
            'scooter' => asset('images/catalog/scooter_electrico.png'),
            'patineta' => asset('images/catalog/patineta.png'),
            'moto' => asset('images/catalog/motocicleta.png'),
            default => asset('images/catalog/motocicleta.png'),
        };
    };

    $isPlaceholder = function ($path) {
        if (empty($path)) return true;
        $str = (string) $path;
        return \Illuminate\Support\Str::contains($str, 'placeholder.com');
    };

    $image = ($vehicle->imagen_principal && !$isPlaceholder($vehicle->imagen_principal))
        ? (\Illuminate\Support\Str::startsWith($vehicle->imagen_principal, ['http://','https://']) ? $vehicle->imagen_principal : asset($vehicle->imagen_principal))
        : $typeImage($tipo);

    $colorHex = function ($name) {
        $n = strtolower(trim((string) $name));
        return match(true) {
            str_starts_with($n, 'rojo') => '#ef4444',
            str_starts_with($n, 'azul') => '#3b82f6',
            str_starts_with($n, 'verde') => '#22c55e',
            str_starts_with($n, 'amarillo') => '#f59e0b',
            str_starts_with($n, 'naranja') => '#f97316',
            str_starts_with($n, 'morado') => '#8b5cf6',
            str_starts_with($n, 'negro') => '#0a0a0a',
            str_starts_with($n, 'blanco') => '#ffffff',
            str_starts_with($n, 'gris') => '#9ca3af',
            default => '#9ca3af',
        };
    };
@endphp

<x-layouts.public>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @include('catalog.partials.breadcrumbs', [
            'items' => [
                ['label' => 'Catálogo', 'href' => route('catalog.index')],
                ['label' => $vehicle->marca.' '.$vehicle->modelo, 'href' => null],
            ],
        ])

        <div class="mt-6 grid lg:grid-cols-2 gap-8">
            <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white/70 dark:bg-white/5 backdrop-blur p-6">
                <div class="aspect-video bg-zinc-50/60 dark:bg-zinc-800/40 rounded-xl flex items-center justify-center">
                    <img src="{{ $image }}" alt="{{ $vehicle->marca.' '.$vehicle->modelo }}" class="w-3/4 object-contain" />
                </div>
            </div>

            <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white/70 dark:bg-white/5 backdrop-blur p-6 flex flex-col justify-center">
                <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">{{ $vehicle->marca.' '.$vehicle->modelo }}</h1>
                <div class="mt-2 text-zinc-600 dark:text-zinc-400">{{ ucfirst($tipo) }}</div>

                <div class="mt-4 flex items-center gap-3">
                    <span class="inline-flex items-center h-7 px-3 rounded text-sm bg-blue-600 text-white">${{ number_format((int) $vehicle->precio_hora, 0, ',', '.') }}/hora</span>
                    @if($vehicle->precio_dia)
                        <span class="inline-flex items-center h-7 px-3 rounded text-sm ring-1 ring-blue-600/20 text-blue-700 dark:text-blue-400">${{ number_format((int) $vehicle->precio_dia, 0, ',', '.') }}/día</span>
                    @endif
                </div>

                <div class="mt-6 grid sm:grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center h-6 px-3 rounded-md bg-blue-600 text-white whitespace-nowrap">{{ ucfirst($tipo) }}</span>
                        <span class="text-zinc-700 dark:text-zinc-300">Tipo</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center h-6 px-3 rounded-md bg-blue-600 text-white whitespace-nowrap">{{ $vehicle->year }}</span>
                        <span class="text-zinc-700 dark:text-zinc-300">Año</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 rounded-md ring-1 ring-zinc-300 dark:ring-white/10" style="background-color: {{ $colorHex($vehicle->color) }}"></span>
                        <span class="text-zinc-700 dark:text-zinc-300">{{ ucfirst($vehicle->color) }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center h-6 px-3 rounded-md bg-blue-600 text-white whitespace-nowrap">{{ $vehicle->branch->ciudad ?? 'Sede' }}</span>
                        <span class="text-zinc-700 dark:text-zinc-300">Ubicación</span>
                    </div>
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <a href="{{ route('catalog.reserve', ['vehicle' => $vehicle->id]) }}" wire:navigate class="inline-flex items-center justify-center h-11 px-5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors text-sm font-semibold whitespace-nowrap">Reservar</a>
                    <a href="{{ route('catalog.index') }}" wire:navigate class="inline-flex items-center justify-center h-11 px-5 rounded-lg ring-1 ring-zinc-300 dark:ring-white/10 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-white/10 transition-colors text-sm font-semibold whitespace-nowrap">Volver</a>
                </div>
            </div>
        </div>

        <div class="mt-10">
            <div class="text-base font-semibold text-zinc-900 dark:text-white">Otros dispositivos que puedes usar</div>
            @php
                $similar = \App\Models\Vehicle::query()
                    ->disponibles()
                    ->visiblesEnCatalogo()
                    ->where('id', '!=', $vehicle->id)
                    ->when($tipo, fn($q) => $q->porTipo($tipo))
                    ->limit(8)
                    ->get();
            @endphp
            @php
                $more = \App\Models\Vehicle::query()
                    ->disponibles()
                    ->visiblesEnCatalogo()
                    ->where('id', '!=', $vehicle->id)
                    ->when($tipo, fn($q) => $q->where(function($qq) use($tipo){ $qq->where('tipo', '!=', $tipo); }))
                    ->latest()
                    ->limit(8)
                    ->get();
                $carousel = $similar->concat($more);
            @endphp
            @if($carousel->isEmpty())
                @include('catalog.partials.empty')
            @else
                <div class="mt-4 overflow-hidden" style="mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);">
                    <div class="flex gap-4 animate-scroll-x" style="width: max-content">
                        @foreach($carousel as $s)
                            @php
                                $tipoS = is_object($s->tipo) ? $s->tipo->value : $s->tipo;
                                $imgS = ($s->imagen_principal && !\Illuminate\Support\Str::contains($s->imagen_principal, 'placeholder.com'))
                                    ? (\Illuminate\Support\Str::startsWith($s->imagen_principal, ['http://','https://']) ? $s->imagen_principal : asset($s->imagen_principal))
                                    : $typeImage($tipoS);
                            @endphp
                            <a href="{{ route('catalog.vehicle.detail', ['id' => $s->id]) }}" wire:navigate class="w-64 shrink-0 rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white/70 dark:bg-white/5 backdrop-blur">
                                <div class="aspect-video bg-zinc-50/60 dark:bg-zinc-800/40 rounded-t-2xl flex items-center justify-center">
                                    <img src="{{ $imgS }}" alt="{{ $s->marca.' '.$s->modelo }}" class="w-44 object-contain" />
                                </div>
                                <div class="p-4">
                                    <div class="text-sm font-semibold text-zinc-900 dark:text-white truncate">{{ $s->marca.' '.$s->modelo }}</div>
                                    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">${{ number_format((int) $s->precio_hora, 0, ',', '.') }}/hora</div>
                                </div>
                            </a>
                        @endforeach
                        @foreach($carousel as $s)
                            @php
                                $tipoS = is_object($s->tipo) ? $s->tipo->value : $s->tipo;
                                $imgS = ($s->imagen_principal && !\Illuminate\Support\Str::contains($s->imagen_principal, 'placeholder.com'))
                                    ? (\Illuminate\Support\Str::startsWith($s->imagen_principal, ['http://','https://']) ? $s->imagen_principal : asset($s->imagen_principal))
                                    : $typeImage($tipoS);
                            @endphp
                            <a href="{{ route('catalog.vehicle.detail', ['id' => $s->id]) }}" wire:navigate class="w-64 shrink-0 rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white/70 dark:bg-white/5 backdrop-blur">
                                <div class="aspect-video bg-zinc-50/60 dark:bg-zinc-800/40 rounded-t-2xl flex items-center justify-center">
                                    <img src="{{ $imgS }}" alt="{{ $s->marca.' '.$s->modelo }}" class="w-44 object-contain" />
                                </div>
                                <div class="p-4">
                                    <div class="text-sm font-semibold text-zinc-900 dark:text-white truncate">{{ $s->marca.' '.$s->modelo }}</div>
                                    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">${{ number_format((int) $s->precio_hora, 0, ',', '.') }}/hora</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                <style>
                @keyframes scroll-x { from { transform: translateX(0); } to { transform: translateX(-50%); } }
                .animate-scroll-x { animation: scroll-x 40s linear infinite; will-change: transform; }
                @media (hover:hover) { .animate-scroll-x:hover { animation-play-state: paused; } }
                </style>
            @endif
        </div>
    </div>
</x-layouts.public>