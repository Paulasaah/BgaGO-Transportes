<?php

namespace App\Contracts;

interface GeocodingService
{
    public function geocode(string $query): array;

    public function search(string $query, int $limit = 5): array;
}
