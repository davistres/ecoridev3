<?php

namespace App\Http\Controllers;

use App\Http\Requests\DemandeRechercheCovoit;
use App\Http\Requests\StoreCovoiturageRequest;
use App\Http\Requests\ModifCovoitRequest;
use App\Models\Covoiturage;
use App\Models\Voiture;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
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
    public function store(StoreCovoiturageRequest $request): JsonResponse|RedirectResponse
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

            if ($request->wantsJson()) {
                return response()->json(['status' => 'trip-created']);
            }
            return Redirect::route('dashboard')->with('status', 'trip-created');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Une erreur est survenue lors de la création du trajet.'], 500);
            }
            return Redirect::route('dashboard')->with('error', 'Une erreur est survenue lors de la création du trajet.');
        }
    }

    public function update(ModifCovoitRequest $request, Covoiturage $covoiturage): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        // Valeur de eco_travel?
        $voiture = Voiture::find($validated['voiture_id']);
        $validated['eco_travel'] = ($voiture && $voiture->energie === 'Electrique') ? 1 : 0;

        try {
            $covoiturage->update($validated);

            if ($request->wantsJson()) {
                return response()->json(['status' => 'trip-updated']);
            }
            return Redirect::route('dashboard')->with('status', 'trip-updated');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Une erreur est survenue lors de la mise à jour du trajet.'], 500);
            }
            return Redirect::route('dashboard')->with('error', 'Une erreur est survenue lors de la mise à jour du trajet.');
        }
    }

    public function destroy(Covoiturage $covoiturage): RedirectResponse
    {
        if (Auth::id() !== $covoiturage->user_id) {
            abort(403, 'Action non autorisée.');
        }

        // Pour concerver toute l'historique, je marque le covoit comme annulé au lieu de supprimer
        $covoiturage->cancelled = 1;
        $covoiturage->save();

        return Redirect::route('dashboard')->with('status', 'trip-cancelled');
    }

    public function getDetails(Covoiturage $covoiturage)
    {
        // Check si l'utilisateur connecté est le propriétaire du covoi
        if (Auth::id() !== $covoiturage->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($covoiturage);
    }
}