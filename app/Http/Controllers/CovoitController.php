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
        $validated = $request->validated();

        // Ajoute l'ID de l'utilisateur
        $validated['user_id'] = Auth::id();

        // available_seats en n_tickets pour correspondre à la base de données
        $validated['n_tickets'] = $validated['available_seats'];
        unset($validated['available_seats']);

        // Voyage écologique
        $voiture = Voiture::find($validated['voiture_id']);
        $validated['eco_travel'] = ($voiture && $voiture->energie === 'Electrique') ? 1 : 0;

        // Statuts => valeurs par défaut
        $validated['trip_started'] = 0;
        $validated['trip_completed'] = 0;
        $validated['cancelled'] = 0;

        try {
            Covoiturage::create($validated);
            return Redirect::route('dashboard')->with('status', 'trip-created');
        } catch (\Exception $e) {
            return Redirect::route('dashboard')->with('error', 'Une erreur est survenue lors de la création du trajet.');
        }
    }
}
