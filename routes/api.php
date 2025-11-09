<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AvisController;

// Route pour tester l'authentification
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

// Route pour récupérer les avis d'un conducteur
Route::get('/avis/conducteur/{driverId}', [AvisController::class, 'getReviews'])->name('api.driver.reviews');
// Pour gagner en vitesse, j'ai décidé de ne pas récupérer les infos en faisant un appel API...
// Mais pour les avis, je pensais que faire la même chose serait trop lent... Donc, je pensias devoir faire un appel api...
// Route pour récupérer les satisfactions en attente
// Mais rien ne marche???? Après ce commit, je vais essayer encore... Mais en cas d'échec, je vais faire comme pour les autres infos... En limitant néanmoins le nombre d'avis à afficher...
Route::get('/user/pending-satisfactions', [App\Http\Controllers\Api\TripDetailsController::class, 'getPendingSatisfactions'])->name('api.user.pending-satisfactions');
