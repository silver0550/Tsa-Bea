<?php

use App\Http\Controllers\TspController;
use App\Models\Bacterium;
use App\Models\City;
use App\Services\BeaSolver;
use Illuminate\Support\Facades\Route;

Route::get('/tsp', [TspController::class, 'index'])->name('tsp.index');
Route::post('/tsp/solve', [TspController::class, 'solve'])->name('tsp.solve');

