<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CovoitController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/accueil', function () {
    return view('welcome');
})->name('accueil');

Route::get('/covoiturage', function () {
    return view('covoiturage');
})->name('covoiturage');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/covoiturage/recherche', [CovoitController::class, 'search'])->name('covoiturage.search');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';