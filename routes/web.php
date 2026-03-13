<?php

use App\Http\Controllers\DfaController;
use App\Http\Controllers\TspController;
use Illuminate\Support\Facades\Route;

Route::get('/tsp', [TspController::class, 'index'])->name('tsp.index');
Route::post('/tsp/solve', [TspController::class, 'solve'])->name('tsp.solve');

Route::get('/dfa', [DfaController::class, 'solve'])->name('dfa.solve');
Route::get('/ll1', [DfaController::class, 'll1Index'])->name('ll1.index');
Route::post('/ll1', [DfaController::class, 'evaluate'])->name('ll1.evaluate');

