<?php

use App\Http\Controllers\Api\CountryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('countries', CountryController::class)->only(['index', 'show']);
