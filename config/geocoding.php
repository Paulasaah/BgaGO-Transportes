<?php

return [
    'default' => env('GEOCODING_PROVIDER', 'nominatim'),

    'providers' => [
        'nominatim' => [
            'base_url' => env('GEOCODING_NOMINATIM_BASE_URL', 'https://nominatim.openstreetmap.org'),
            'email' => env('GEOCODING_NOMINATIM_EMAIL'),
            'timeout' => (int) env('GEOCODING_TIMEOUT', 5),
            'countrycodes' => env('GEOCODING_COUNTRYCODES', 'co'),
            // Opcional: restringir búsqueda a un bounding box (viewbox) tipo: left,top,right,bottom
            // Por defecto se usa un área alrededor de Bucaramanga / AMB. 
            // Puedes sobreescribirlo en .env con GEOCODING_VIEWBOX si necesitas otra región.
            'viewbox' => env('GEOCODING_VIEWBOX', '-73.25,7.20,-73.00,7.00'),
        ],
    ],
];
