{{--
    Vista: Catálogo Secundario CORREGIDO
    Propósito: Catálogo que renderiza correctamente sin errores
    Ubicación: resources/views/landing/catalogo_secundario.blade.php
--}}

<section class="py-24 min-h-screen bg-gradient-to-br from-white via-blue-50 to-blue-200 overflow-hidden"    >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <x-catalog.header
            title="Catálogo de Transporte Ecológico"
            subtitle="Explora nuestra flota de bicicletas, motos y patinetas eléctricas disponibles para préstamo."
        />

        {{-- Filters --}}
        <x-catalog.filters
            :filters="['Todos', 'Bicicletas', 'Motos', 'Patinetas']"
            activeFilter="Todos"
        />

        {{-- Vehicle Grid dinámico --}}
        @php
            use Illuminate\Support\Str;

            $tipo = request('tipo');
            $vehicles = \App\Models\Vehicle::visiblesEnCatalogo()
                ->when($tipo, fn($q) => $q->porTipo($tipo))
                ->with('branch')
                ->orderBy('marca')
                ->get();

            $iconForType = function(\App\Enums\VehicleType $tipo) {
                return $tipo->value === 'patineta' ? 'skate' : 'electric';
            };

            $statusFor = function(\App\Enums\VehicleStatus $estado) {
                return $estado->isAvailable() ? 'available' : 'coming-soon';
            };
        @endphp

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @forelse($vehicles as $v)
                @php
                    $specs = array_values(array_filter([
                        $v->tipo->label(),
                        $v->color ? ucfirst($v->color) : null,
                        $v->year ? (string) $v->year : null,
                        $v->branch ? $v->branch->nombre : null,
                    ]));
                @endphp

                <x-cards.vehicle-card
                    :name="sprintf('%s %s', $v->marca, $v->modelo)"
                    :description="$v->descripcion ?? 'Vehículo disponible en nuestra flota.'"
                    :iconType="$iconForType($v->tipo)"
                    :status="$statusFor($v->estado)"
                    :price="$v->precio_hora ?: $v->tipo->baseHourlyRate()"
                    :priceDay="$v->precio_dia ?: $v->tipo->baseDailyRate()"
                    :plate="$v->placa"
                    :specs="$specs"
                    :vehicleSlug="Str::slug($v->marca.'-'.$v->modelo.'-'.$v->placa)"
                />
            @empty
                {{-- Fallback cuando no hay vehículos --}}
                <x-cards.vehicle-card
                    name="Próximamente"
                    description="Estamos trabajando en agregar más opciones a nuestro catálogo."
                    iconType="coming-soon"
                    status="coming-soon"
                />
            @endforelse
        </div>

        {{-- CTA --}}
        @guest
            <x-catalog.cta
                title="¿Listo para reservar?"
                subtitle="Crea tu cuenta y comienza a disfrutar de nuestros vehículos ecológicos hoy mismo."
                ctaText="Crear Cuenta Gratis"
                ctaRoute="register"
            />
        @endguest

    </div>
</section>
