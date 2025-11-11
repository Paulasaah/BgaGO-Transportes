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
     SERVICES SECTION
========================================= --}}
<x-landing.services-grid
    title="Nuestros Servicios"
    subtitle="Todo lo que necesitas para moverte por la ciudad de forma inteligente y sostenible"
    :services="[
        [
            'icon' => '<svg class=\"w-8 h-8 text-white\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\" aria-hidden=\"true\">
                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\" />
            </svg>',
            'title' => 'Préstamo de Vehículos',
            'description' => 'Reserva motos, bicicletas, patinetas eléctricas y más. Elige tu vehículo favorito y móvete por la ciudad a tu ritmo.',
            'features' => [
                ['text' => 'Variedad de vehículos ecológicos'],
                ['text' => 'Reserva anticipada disponible'],
                ['text' => 'Precios accesibles por hora/día']
            ],
            'ctaText' => 'Ver catálogo',
            'ctaRoute' => 'catalogo'
        ],
        [
            'icon' => '<svg class=\"w-8 h-8 text-white\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\" aria-hidden=\"true\">
                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\" />
            </svg>',
            'title' => 'Servicio de Domicilios',
            'description' => 'Envía paquetes y documentos de forma rápida y segura. Mensajería local confiable en todo el Área Metropolitana.',
            'features' => [
                ['text' => 'Entregas en menos de 60 minutos'],
                ['text' => 'Conductores verificados'],
                ['text' => 'Rastreo GPS en tiempo real']
            ],
            'ctaText' => 'Enviar paquete',
            'ctaRoute' => 'register'
        ],
        [
            'icon' => '<svg class=\"w-8 h-8 text-white\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\" aria-hidden=\"true\">
                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7\" />
            </svg>',
            'title' => 'Seguimiento en Vivo',
            'description' => 'Rastrea tu vehículo o domicilio en tiempo real. Transparencia total desde que solicitas hasta que llegas a tu destino.',
            'features' => [
                ['text' => 'GPS de alta precisión'],
                ['text' => 'Actualizaciones instantáneas'],
                ['text' => 'Historial de rutas']
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
