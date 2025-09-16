<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TripDetailsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes pour la modale détails (au clic sur le btn "Détails" d'un covoiturage-card)
Route::get('/trips/{id}/details', [TripDetailsController::class, 'getDetails']);
Route::get('/trips/{tripId}/user-status', [TripDetailsController::class, 'getUserStatus']);
