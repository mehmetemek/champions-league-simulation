<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\FixtureController;
use App\Http\Controllers\Api\SimulationController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/teams', [TeamController::class, 'index']);
Route::post('/fixtures/generate', [FixtureController::class, 'generate']);
Route::get('/fixtures', [FixtureController::class, 'index']);


Route::prefix('simulation')->group(function () {
    Route::post('/play-week/{week}', [SimulationController::class, 'playWeek']);
    Route::post('/play-all-weeks', [SimulationController::class, 'playAllWeeks']);
    Route::post('/reset-data', [SimulationController::class, 'resetData']);
    Route::get('/current-state/{week?}', [SimulationController::class, 'getCurrentState']);
});