<x-layouts.portal>
    <x-landing.hero
        title='Movilidad Inteligente para <span class="text-blue-600 dark:text-blue-400">Bucaramanga</span>'
        subtitle="Conecta con vehículos ecológicos, recibe domicilios rápidos y rastrea todo en tiempo real. La nueva forma de moverte por el Área Metropolitana."
        :primaryCta="['text' => 'Comenzar Ahora', 'url' => '/register']"
        :secondaryCta="['text' => 'Ver Catálogo', 'url' => '/catalog']"
        :stats="[
            ['value' => '4', 'label' => 'Sedes Activas'],
            ['value' => '50+', 'label' => 'Vehículos'],
            ['value' => '1K+', 'label' => 'Usuarios'],
            ['value' => '24/7', 'label' => 'Disponibilidad']
        ]"
    />

    <x-landing.services-grid
        title="Nuestros Servicios"
        subtitle="Todo lo que necesitas para moverte por la ciudad de forma inteligente y sostenible"
        :services="[
            [
                'iconType' => 'prestamo',
                'title' => 'Préstamo de Vehículos',
                'description' => 'Reserva motos, bicicletas, patinetas eléctricas y más. Elige tu vehículo favorito y muévete por la ciudad a tu ritmo.',
                'features' => [
                    'Variedad de vehículos ecológicos',
                    'Reserva anticipada disponible',
                    'Precios accesibles por hora/día'
                ],
                'ctaText' => 'Ver catálogo',
                'ctaUrl' => '/catalog'
            ],
            [
                'iconType' => 'domicilio',
                'title' => 'Servicio de Domicilios',
                'description' => 'Envía paquetes y documentos de forma rápida y segura. Mensajería local confiable en todo el Área Metropolitana.',
                'features' => [
                    'Entregas en menos de 60 minutos',
                    'Conductores verificados',
                    'Rastreo GPS en tiempo real'
                ],
                'ctaText' => 'Enviar paquete',
                'ctaUrl' => '/register'
            ],
            [
                'iconType' => 'gps',
                'title' => 'Seguimiento en Vivo',
                'description' => 'Rastrea tu vehículo o domicilio en tiempo real. Transparencia total desde que solicitas hasta que llegas a tu destino.',
                'features' => [
                    'GPS de alta precisión',
                    'Actualizaciones instantáneas',
                    'Historial de rutas'
                ],
                'ctaText' => 'Ver mapa',
                'ctaUrl' => '/mapa'
            ]
        ]"
    />

    <x-landing.locations-section
        title="Nuestras Sedes"
        subtitle="Estamos presentes en los puntos estratégicos del Área Metropolitana"
        mapUrl="/mapa"
        :locations="[
            ['name'=>'Cabecera','zone'=>'Bucaramanga','address'=>'Cra 36 #45-12','phone'=>'300 123 4567','schedule'=>'8:00-20:00'],
            ['name'=>'Centro','zone'=>'Bucaramanga','address'=>'Cra 19 #35-10','phone'=>'300 987 6543','schedule'=>'8:00-20:00'],
            ['name'=>'La Flora','zone'=>'Floridablanca','address'=>'Cra 7 #8-30','phone'=>'300 456 7890','schedule'=>'8:00-20:00'],
            ['name'=>'El Poblado','zone'=>'Girón','address'=>'Cra 23 #20-15','phone'=>'300 654 3210','schedule'=>'8:00-20:00']
        ]"
    />

    <x-landing.about-section
        title="Conectando el Área Metropolitana de Bucaramanga"
        :description="[
            'BgaGO nace con la misión de transformar la movilidad urbana en Bucaramanga y su área metropolitana. Creemos en un transporte más sostenible, accesible y tecnológico que mejore la calidad de vida de todos los bumangueses.',
            'Con presencia en las principales zonas de la ciudad, ofrecemos soluciones innovadoras de movilidad que combinan tecnología de punta con un servicio cercano y confiable.'
        ]"
        :features="[
            ['value' => '4', 'label' => 'Sedes en operación'],
            ['value' => '100%', 'label' => 'Vehículos ecológicos']
        ]"
    />
</x-layouts.portal>
