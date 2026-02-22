<?php

namespace App\Services;

use App\Models\Bacterium;
use App\Models\CityContainer;
use Illuminate\Support\Collection;
use Random\RandomException;

class BeaSolver
{
    private Collection $population;

    public function __construct(
        private readonly CityContainer $cities,
        private readonly int           $populationSize = 100,
        private readonly int           $generations = 400,
        private readonly float         $eliteFraction = 0.05,
        private readonly int           $tournamentSize = 3,
        private readonly float         $crossoverRate = 0.90,
        private readonly float         $mutationRate = 0.30,
    )
    {
        $this->population = collect();
    }

    public function solve(): Bacterium
    {
        $this->initPopulation();

        $best = $this->bestOf($this->population);

        for ($g = 0; $g < $this->generations; $g++) {
            $this->population = $this->nextGeneration($this->population);

            $genBest = $this->bestOf($this->population);
            if ($genBest->fitness() < $best->fitness()) {
                $best = $genBest;
            }
        }

        return $best;
    }

    private function initPopulation(): void
    {
        $countOfCities = $this->cities->count();
        if ($countOfCities < 4) {
            throw new \InvalidArgumentException('At least 4 cities are recommended.');
        }

        $this->population = collect();

        for ($i = 0; $i < $this->populationSize; $i++) {
            $route = collect(range(0, $countOfCities - 1))->shuffle()->values();
            $b = new Bacterium($this->cities, $route);

            $this->population->push($b);
        }
    }

    private function nextGeneration(Collection $population): Collection
    {
        $sorted = $population->sortBy(fn(Bacterium $b) => $b->fitness())->values();

        $eliteCount = max(1, (int)floor($this->populationSize * $this->eliteFraction));
        $next = $sorted->take($eliteCount)->map(fn(Bacterium $b) => $b->copy())->values();

        while ($next->count() < $this->populationSize) {
            $parentA = $this->tournamentSelect($sorted);
            $parentB = $this->tournamentSelect($sorted);

            // 1) crossover vagy klónozás
            if ($this->randFloat() < $this->crossoverRate) {
                $child = $parentA->crossWith($parentB);
            } else {
                $child = $parentA->copy();
            }

            // 2) mutáció
            if ($this->randFloat() < $this->mutationRate) {
                $child = $child->mutate();
            }

            $next->push($child);
        }

        return $next->values();
    }

    private function tournamentSelect(Collection $sortedPopulation): Bacterium
    {
        /** @var Collection<int, Bacterium> $contenders */
        $contenders = $sortedPopulation->random(
            min($this->tournamentSize, $sortedPopulation->count())
        );

        return $contenders->sortBy(fn(Bacterium $bacterium) => $bacterium->fitness())->first();

    }

    private function bestOf(Collection $population): Bacterium
    {
        /** @var Bacterium $best */
        return $population->sortBy(fn(Bacterium $b) => $b->fitness())->first();
    }

    /**
     * @throws RandomException
     */
    private function randFloat(): float
    {
        return random_int(0, PHP_INT_MAX) / PHP_INT_MAX;
    }
}
