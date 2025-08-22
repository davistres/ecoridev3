<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CovoitController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VoitureController;
use App\Models\Voiture;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/accueil', function () {
    return view('welcome');
})->name('accueil');

Route::get('/covoiturage', [CovoitController::class, 'index'])->name('covoiturage');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/credits/recharge', [DashboardController::class, 'recharge'])->name('credits.recharge');
    Route::patch('/profile/role', [ProfileController::class, 'newRole'])->name('profile.role.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/driverinfo', [ProfileController::class, 'driverInfo'])->name('profile.driverinfo.store');
    Route::patch('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences.update');

    Route::resource('voitures', VoitureController::class)->only([
        'store',
        'update',
        'destroy'
    ]);

    Route::resource('covoiturages', CovoitController::class)->only([
        'store'
    ]);

    // Route pour vérifier les covoits futurs d'une voiture
    Route::get('/voitures/{voiture}/has-future-carpools', function (Voiture $voiture) {
        if (auth()->id() !== $voiture->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $hasFutureCarpools = $voiture->covoiturages()->where('departure_date', '>=', now()->toDateString())->exists();
        return response()->json(['has_future_carpools' => $hasFutureCarpools]);
    })->name('voitures.hasFutureCarpools');
});

require __DIR__ . '/auth.php';