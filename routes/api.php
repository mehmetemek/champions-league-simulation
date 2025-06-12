<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\FixtureController; // FixtureController'ı import edin

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/teams', [TeamController::class, 'index']);

Route::post('/fixtures/generate', [FixtureController::class, 'generate']);
Route::get('/fixtures', [FixtureController::class, 'index']);