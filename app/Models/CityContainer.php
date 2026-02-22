<?php

namespace App\Models;

use Illuminate\Support\Collection;

final class CityContainer
{
    private Collection $cities;

    public function __construct(array $cities)
    {
        $this->cities = collect(array_map(fn ($city) => City::fromArray($city), $cities))->values();
    }

    public function count(): int
    {
        return $this->cities->count();
    }

    public function getCity(int $index): City
    {
        return $this->cities->get($index);
    }
}
