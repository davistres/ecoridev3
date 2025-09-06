<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVoitureRequest;
use App\Http\Requests\UpdateVoitureRequest;
use App\Models\Covoiturage;
use App\Models\Voiture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class VoitureController extends Controller
{
    /** fonction store => ajout d'une nouvelle voiture à la table VOITURE en ajoutant un id pour lier l'utilisateur à la voiture */
    public function store(StoreVoitureRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        $voiture = Voiture::create($validated);

        /** addcovoit-addvehicle-modal envoie une demande une Ajax */
        /** Ici donc, on vérifie que c'est bien le cas pour retourner du JSON */
        /** Je n'ai pas compris cette logique!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Mais pour cela, on doit vérifier deux choses avec: */
        /** expectsJson() vérifie si la requête attend une réponse JSON  et ajax() vérifie si la requête est une requête AJAX. */
        /** ajax() je comprends!!!! Mais pourquoi expectsJson()???? */
        /** expectsJson() est une méthode de la classe Request de Laravel qui vérifie si la requête attend une réponse JSON. ajax() vérifie si la requête est une requête AJAX. */
        /** TODO! Je dois essayer de creuser ceci pour comprendre */
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'voiture' => $voiture]);
        }

        /** Si ce n'est pas une requête Ajax, redirection normale (depuis add-vehicle-modal) */
        return Redirect::route('dashboard')->with('status', 'vehicle-added');
    }

    /** Mise à jour des infos d'une voiture */
    public function update(UpdateVoitureRequest $request, Voiture $voiture): RedirectResponse
    {
        // Au cas où... On vérifie que la voiture appartient bien à l'utilisateur
        if (Auth::id() !== $voiture->user_id) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validated();
        $voiture->update($validated);

        return Redirect::route('dashboard')->with('status', 'vehicle-updated');
    }

    /** Suppr la voiture et toutes ses infos*/
    public function destroy(Voiture $voiture): RedirectResponse
    {
        $user = Auth::user();

        if ($user->user_id !== $voiture->user_id) {
            abort(403, 'Action non autorisée.');
        }

        DB::transaction(function () use ($voiture, $user) {
            // Suppr tous les covoits futurs liés à cette voiture
            Covoiturage::where('voiture_id', $voiture->voiture_id)
                ->where('departure_date', '>=', now()->toDateString())
                ->delete();

            // Suppr la voiture
            $voiture->delete();

            // C'est le dernier véhicule?
            $remainingVehicles = Voiture::where('user_id', $user->user_id)->count();

            if ($remainingVehicles === 0 && in_array($user->role, ['Conducteur', 'Les deux'])) {
                $user->role = 'Passager';
                // Réinit les préférences conducteur
                $user->pref_smoke = null;
                $user->pref_pet = null;
                $user->pref_libre = null;
                $user->save();
            }
        });

        // Message géré par Js
        return Redirect::route('dashboard')->with('status', 'vehicle-deleted');
    }

    // Suppr un véhicule temporaire (créé depuis addcovoit-addvehicle-modal mais covoit non validé)
    public function destroyTemporary(Voiture $voiture): JsonResponse
    {
        // La voiture appartient à l'utilisateur?
        if (Auth::id() !== $voiture->user_id) {
            abort(403, 'Action non autorisée.');
        }

        // Cette voiture est utilisée dans un covoit?
        if ($voiture->covoiturages()->exists()) {
            return response()->json(['success' => false, 'message' => 'Ce véhicule est utilisé dans des covoiturages.'], 400);
        }

        $voiture->delete();

        return response()->json(['success' => true, 'message' => 'Véhicule temporaire supprimé.']);
    }
}