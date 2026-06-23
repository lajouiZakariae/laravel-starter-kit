<?php

use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CountryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('countries', CountryController::class)->only(['index', 'show']);

Route::apiResource('cities', CityController::class)->only(['index', 'show']);

require __DIR__ . '/api/auth/index.php';
