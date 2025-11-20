<?php

return [
    // Velocidad promedio en ciudad (km/h) usada para estimar tiempos mínimos de domicilios
    // Se usa para evitar tiempos de OSRM demasiado optimistas.
    'city_speed_kmh' => env('DELIVERY_CITY_SPEED_KMH', 25),

    // Distancia máxima permitida para un domicilio (en km). 0 desactiva la validación.
    'max_distance_km' => env('DELIVERY_MAX_DISTANCE_KM', 0),
];
