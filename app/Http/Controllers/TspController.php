<?php

namespace App\Http\Controllers;

use App\Http\Requests\SolveRequest;
use App\Models\City;
use App\Models\CityContainer;
use App\Services\BeaSolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TspController
{
    public function index(): View
    {
        return view('tsp');
    }

    public function solve(SolveRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $cities = new CityContainer($validated['cities']);

        $solver = new BeaSolver(
            cities: $cities,
            populationSize: 160,
            generations:  700,
            eliteFraction: 0.05,
            tournamentSize: 3,
            crossoverRate: 0.9,
            mutationRate: 0.3
        );

        $best = $solver->solve();

        return response()->json([
            'bestFitness' => $best->fitness(),
            'bestRoute' => $best->getRoute()->values()->toArray(),
        ]);
    }
}
