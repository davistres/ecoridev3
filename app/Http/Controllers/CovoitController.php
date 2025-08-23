<?php

namespace App\Http\Controllers;

use App\Http\Requests\DemandeRechercheCovoit;
use App\Http\Requests\StoreCovoiturageRequest;
use App\Models\Covoiturage;
use App\Models\Voiture;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CovoitController extends Controller
{
    public function index()
    {
        return view('covoiturage');
    }

    public function search(DemandeRechercheCovoit $request)
    {
        $validated = $request->validated();

        // Pour le moment => données validées.
        // TODO => la recherche en base de données.
        dd($validated);
    }

    // Stock les nouveaux covoit créés
    public function store(StoreCovoiturageRequest $request): RedirectResponse
    {
        // On récupére ici toutes les données déjà validées par StoreCovoiturageRequest
        $validated = $request->validated();

        // On ajoute l'id de l'utilisateur connecté
        $validated['user_id'] = Auth::id();

        // On détermine la valeur de eco_travel
        $voiture = Voiture::find($validated['voiture_id']);
        $validated['eco_travel'] = ($voiture && $voiture->energie === 'Electrique') ? 1 : 0;

        try {
            // On enregistre le covoitdans la base de donnée
            Covoiturage::create($validated);
            return Redirect::route('dashboard')->with('status', 'trip-created');
        } catch (\Exception $e) {
            // En cas d'erreur
            return Redirect::route('dashboard')->with('error', 'Une erreur est survenue lors de la création du trajet.');
        }
    }
}