<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'teamSelection']);
Route::get('/fixtures', [HomeController::class, 'fixtureDisplay']);
Route::get('/simulation', [HomeController::class, 'simulation']);