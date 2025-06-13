<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'teamSelection'])->name('team.selection');
Route::get('/fixtures', [HomeController::class, 'fixtureDisplay'])->name('fixture.display');
Route::get('/simulation', [HomeController::class, 'simulation'])->name('simulation');