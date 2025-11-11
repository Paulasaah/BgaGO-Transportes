{{--
    Vista: Home Landing Page
    Propósito: Página de inicio modular con diseño EXACTO del original
    Ubicación: resources/views/landing/home.blade.php
--}}

@extends('layouts.landing')

@section('content')

{{-- =========================================
     HERO SECTION
========================================= --}}
<x-landing.hero
    title='Movilidad Inteligente para <span class="text-blue-700">Bucaramanga</span>'
    subtitle="Conecta con vehículos ecológicos, recibe domicilios rápidos y rastrea todo en tiempo real. La nueva forma de moverte por el Área Metropolitana."
    :primaryCta="[
        'text' => 'Comenzar Ahora',
        'route' => 'register'
    ]"
    :secondaryCta="[
        'text' => 'Ver Catálogo',
        'route' => 'catalogo'
    ]"
    :stats="[
        ['value' => '4', 'label' => 'Sedes Activas'],
        ['value' => '50+', 'label' => 'Vehículos'],
        ['value' => '1K+', 'label' => 'Usuarios'],
        ['value' => '24/7', 'label' => 'Disponibilidad']
    ]"
/>

{{-- =========================================
     ABOUT SECTION
========================================= --}}
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

{{-- =========================================
     SERVICES SECTION - CORREGIDO
========================================= --}}
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
            'ctaRoute' => 'catalogo'
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
            'ctaRoute' => 'register'
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
            'ctaRoute' => 'mapa'
        ]
    ]"
/>

{{-- =========================================
     LOCATIONS SECTION
========================================= --}}
<x-landing.locations-section
    title="Nuestras Sedes"
    subtitle="Estamos presentes en los puntos estratégicos del Área Metropolitana"
    mapRoute="mapa"
    :locations="[
        [
            'name' => 'Cabecera',
            'zone' => 'Sede Principal',
            'address' => 'Cra. 33 #42-90, Cabecera',
            'phone' => '+57 300 111 1111',
            'schedule' => 'Lun - Dom: 6:00 AM - 10:00 PM',
            'status' => 'open',
            'variant' => 'primary'
        ],
        [
            'name' => 'Cañaveral',
            'zone' => 'Zona Comercial',
            'address' => 'Cra. 27 #123-45, Cañaveral',
            'phone' => '+57 300 222 2222',
            'schedule' => 'Lun - Dom: 7:00 AM - 9:00 PM',
            'status' => 'open',
            'variant' => 'default'
        ],
        [
            'name' => 'Piedecuesta',
            'zone' => 'Centro Histórico',
            'address' => 'Calle 10 #15-30, Piedecuesta',
            'phone' => '+57 300 333 3333',
            'schedule' => 'Lun - Dom: 6:30 AM - 9:30 PM',
            'status' => 'open',
            'variant' => 'secondary'
        ],
        [
            'name' => 'Floridablanca',
            'zone' => 'Zona Sur',
            'address' => 'Calle 52 #5-80, Floridablanca',
            'phone' => '+57 300 444 4444',
            'schedule' => 'Lun - Dom: 7:00 AM - 10:00 PM',
            'status' => 'open',
            'variant' => 'tertiary'
        ]
    ]"
/>

@endsection
