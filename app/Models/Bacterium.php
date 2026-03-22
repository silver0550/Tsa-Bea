<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Random\RandomException;

final class Bacterium extends BaseBacterium
{
    public function copy(): static
    {
        return new self(
            cities: $this->cities,
            route: $this->route
        );
    }

    protected function computeFitness(): float
    {
        $route = $this->route;
        $count = $route->count();

        if ($count < 2) {
            return 0.0;
        }

        $pairs = $route->map(function ($current, $index) use ($route, $count) {
            $next = $route[($index + 1) % $count];
            return [$current, $next];
        });

        return $pairs->reduce(function ($carry, $pair) {
            [$aIndex, $bIndex] = $pair;

            return $carry +
                $this->cities->getCity($aIndex)
                    ->distanceTo($this->cities->getCity($bIndex));
        }, 0.0);
    }

    /**
     * @throws RandomException
     */
    protected function mutateRoute(Collection $route): Collection
    {
        $roll = random_int(1, 100);

        if ($roll <= 70) {
            return $this->mutationInversion($route);
        }

        if ($roll <= 90) {
            return $this->mutationSwap($route);
        }

        return $this->mutationInsert($route);
    }

    /**
     * @throws RandomException
     */
    private function mutationSwap(Collection $route): Collection
    {
        $routes = $route->values();
        $count = $routes->count();

        if ($count < 2) {
            return $routes;
        }

        $i = random_int(0, $count - 1);
        do {
            $j = random_int(0, $count - 1);
        } while ($j === $i);

        $tmp = $routes[$i];
        $routes[$i] = $routes[$j];
        $routes[$j] = $tmp;

        return $routes->values();
    }

    /**
     * @throws RandomException
     */
    private function mutationInversion(Collection $route): Collection
    {
        $routes = $route->values();
        $count = $routes->count();

        if ($count < 2) {
            return $routes;
        }

        $i = random_int(0, $count - 2);
        $j = random_int($i + 1, $count - 1);

        $prefix = $routes->slice(0, $i);
        $middle = $routes->slice($i, $j - $i + 1)->reverse();
        $suffix = $routes->slice($j + 1);

        return $prefix->concat($middle)->concat($suffix)->values();
    }

    /**
     * @throws RandomException
     */
    private function mutationInsert(Collection $route): Collection
    {
        $routes = $route->values();
        $count = $routes->count();

        if ($count < 2) {
            return $routes;
        }

        $i = random_int(0, $count - 1);
        do {
            $j = random_int(0, $count - 1);
        } while ($j === $i);

        $value = $routes[$i];
        $routes = $routes->forget($i)->values();

        $j = min($j, $routes->count());

        $prefix = $routes->slice(0, $j);
        $suffix = $routes->slice($j);

        return $prefix->concat(collect([$value]))->concat($suffix)->values();
    }

    /**
     * @throws RandomException
     */
    protected function crossoverRoute(Collection $routeA, Collection $routeB): Collection
    {
        $a = $routeA->values();
        $b = $routeB->values();

        $n = $a->count();
        if ($n !== $b->count() || $n < 2) {
            return $a;
        }

        $start = random_int(0, $n - 2);
        $end = random_int($start + 1, $n - 1);

        $child = array_fill(0, $n, -1);

        for ($i = $start; $i <= $end; $i++) {
            $child[$i] = $a[$i];
        }

        $used = array_flip(array_values(array_filter($child, fn($v) => $v !== -1)));

        $bItems = $b->toArray();

        $pos = ($end + 1) % $n;
        foreach ($bItems as $gene) {
            if (isset($used[$gene])) {
                continue;
            }

            while ($child[$pos] !== -1) {
                $pos = ($pos + 1) % $n;
            }

            $child[$pos] = $gene;
            $used[$gene] = true;
        }

        return collect($child)->values();
    }
}
