<?php

namespace App\Models;

use Illuminate\Support\Collection;

abstract class BaseBacterium
{
    protected ?float $fitnessCache = null;

    public function __construct(
        protected CityContainer $cities,
        protected Collection $route
    ) {
        $this->validate();
    }

    public function getRoute(): Collection
    {
        return $this->rotateRouteToStart($this->route);
    }

    public function setRoute(Collection $route): void
    {
        $this->route = $route;
        $this->invalidate();
    }

    public function fitness(): float
    {
        if ($this->fitnessCache === null) {
            $this->fitnessCache = $this->computeFitness();
        }

        return $this->fitnessCache;
    }

    public function invalidate(): void
    {
        $this->fitnessCache = null;
    }

    public function validate(): void
    {
        $countOfRoute = count($this->route);
        $countOfCities = $this->cities->count();
        if ($countOfRoute !== $countOfCities) {
            throw new \InvalidArgumentException('Route length mismatch.');
        }
    }

    public function mutate(): static
    {
        $mutatedRoute = $this->mutateRoute($this->route);

        $child = $this->copy();
        $child->setRoute($mutatedRoute);

        return $child;
    }

    public function crossWith(self $other): static
    {
        $childRoute = $this->crossoverRoute($this->route, $other->getRoute());

        $child = $this->copy();
        $child->setRoute($childRoute);

        return $child;
    }

    private function rotateRouteToStart(Collection $route): Collection
    {
        $pos = $route->search(0, strict: true);
        if ($pos === false) {
            return $this->route;
        }

        return $route
            ->slice($pos)
            ->concat($route->slice(0, $pos))
            ->values();
    }

    abstract public function copy(): static;
    abstract protected function computeFitness(): float;
    abstract protected function mutateRoute(Collection $route): Collection;
    abstract protected function crossoverRoute(Collection $routeA, Collection $routeB): Collection;

}
