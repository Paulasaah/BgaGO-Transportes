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
            :filters="['Todos', 'Bicicletas', 'Motos', 'Patinetas', 'Patines']"
            activeFilter="Todos"
        />

        {{-- Vehicle Grid --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">

            {{-- Bicicleta Eléctrica --}}
            <x-cards.vehicle-card
                name="Bicicleta Eléctrica"
                description="Perfecta para trayectos urbanos. Autonomía de 50km."
                iconType="electric"
                status="available"
                :price="5000"
                :specs="['Eléctrica', '50km', '18kg']"
                vehicleSlug="bicicleta-electrica"
            />

            {{-- Moto Eléctrica --}}
            <x-cards.vehicle-card
                name="Moto Eléctrica"
                description="Mayor velocidad y confort. Autonomía de 80km."
                iconType="electric"
                status="available"
                :price="15000"
                :specs="['Eléctrica', '80km', '65kg']"
                vehicleSlug="moto-electrica"
            />

            {{-- Patineta Eléctrica --}}
            <x-cards.vehicle-card
                name="Patineta Eléctrica"
                description="Ágil y compacta. Ideal para distancias cortas."
                iconType="electric"
                status="available"
                :price="3500"
                :specs="['Eléctrica', '30km', '12kg']"
                vehicleSlug="patineta-electrica"
            />

            {{-- Bicicleta Manual --}}
            <x-cards.vehicle-card
                name="Bicicleta Manual"
                description="Clásica y económica. Perfecta para ejercitarte."
                iconType="manual"
                status="available"
                :price="2500"
                :specs="['Manual', 'Ejercicio', '14kg']"
                vehicleSlug="bicicleta-manual"
            />

            {{-- Patines en Línea --}}
            <x-cards.vehicle-card
                name="Patines en Línea"
                description="Diversión y deporte. Incluye equipo de protección."
                iconType="skate"
                status="available"
                :price="4000"
                :specs="['Manual', 'Protección', '3kg']"
                vehicleSlug="patines-linea"
            />

            {{-- Coming Soon --}}
            <x-cards.vehicle-card
                name="Próximamente"
                description="Estamos trabajando en agregar más opciones a nuestro catálogo."
                iconType="coming-soon"
                status="coming-soon"
            />

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
