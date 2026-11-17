<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\Api\DestinationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:'.TokenAbility::ReadDestinations->value])
    ->get('/destinations', [DestinationController::class, 'index']);
