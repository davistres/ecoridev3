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
        $user = Auth::user();

        // Recherche une voiture avec la même immat, y compris les "soft-deleted"
        $existingVoiture = Voiture::withTrashed()->where('immat', $validated['immat'])->first();

        $contactLink = '<a href="' . route('contact') . '" class="font-bold underline">contactez-nous</a>';

        if ($existingVoiture) {
            $errorMessage = null;

            // CAS 1: La voiture existe et est active (pas soft-deleted)
            if (!$existingVoiture->trashed()) {
                // CAS 1.1: La voiture appartient à l'utilisateur actuel
                if ($existingVoiture->user_id == $user->user_id) {
                    $errorMessage = "Vous avez déjà enregistré un véhicule ayant la même plaque d’immatriculation ! Si vous avez commis une erreur, vous devez d’abord la corriger avant de saisir le bon véhicule.";
                }
                // CAS 1.2: La voiture est déjà attribuée à un autre utilisateur
                else {
                    $errorMessage = "Êtes-vous sûr du numéro de plaque ? Cette plaque est déjà utilisée. Si vous pensez qu'il s'agit d'une erreur, " . $contactLink . ".";
                }
            }
            // CAS 2: La voiture existe mais a été soft-deleted
            elseif ($existingVoiture->user_id != $user->user_id) {
                $errorMessage = "Êtes-vous sûr du numéro de plaque ? Cette plaque a déjà été enregistrée par un autre utilisateur. Si vous pensez qu'il s'agit d'une erreur, " . $contactLink . ".";
            }

            // Si y a un message d'erreur => on arrête tout
            if ($errorMessage) {
                // Requete AJAX
                if ($request->wantsJson()) {
                    return response()->json(['errors' => ['immat' => [$errorMessage]]], 422);
                }
                // Standards
                return Redirect::back()->withErrors(['immat' => $errorMessage])->withInput();
            }

            // CAS 3: La voiture existait, mais elle est soft-deleted + elle appartenait à l'utilisateur => on la restaure
            if ($existingVoiture->user_id == $user->user_id) {
                $existingVoiture->restore();
                $existingVoiture->update($validated);
                $voiture = $existingVoiture;
            }
        } else {
            // CAS 4: La voiture n'existe pas du tout -> on la crée
            $validated['user_id'] = $user->user_id;
            $voiture = Voiture::create($validated);
        }

        /** addcovoit-addvehicle-modal envoie une demande une Ajax */
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'voiture' => $voiture]);
        }

        /** Si ce n'est pas une requête Ajax, redirection normale (depuis add-vehicle-modal) */
        return Redirect::route('dashboard')->with('status', 'vehicle-added');
    }

    /** Mise à jour des infos d'une voiture */
    public function update(UpdateVoitureRequest $request, Voiture $voiture): JsonResponse|RedirectResponse
    {
        // Au cas où... On vérifie que la voiture appartient bien à l'utilisateur
        if (Auth::id() !== $voiture->user_id) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validated();
        $user = Auth::user();

        // La plaque d'immat existe t-elle déjà avec un autre véhicule?
        $existingVoiture = Voiture::withTrashed()
            ->where('immat', $validated['immat'])
            ->where('voiture_id', '!=', $voiture->voiture_id) // le véhicule actuel est exclu de la recherche
            ->first();

        if ($existingVoiture) {
            $errorMessage = null;
            $contactLink = '<a href="' . route('contact') . '" class="font-bold underline">contactez-nous</a>';

            // CAS 1: La plaque appartient déjà à un autre véhicule de l'utilisateur
            if ($existingVoiture->user_id == $user->user_id) {
                $errorMessage = "Vous avez déjà enregistré un véhicule ayant la même plaque d’immatriculation ! Si vous avez commis une erreur, vous devez d’abord la corriger avant de saisir le bon véhicule.";
            }
            // CAS 2: La plaque appartient à un autre utilisateur
            else {
                $errorMessage = "Êtes-vous sûr du numéro de plaque ? Cette plaque est déjà utilisée. Si vous pensez qu'il sagit d'une erreur, " . $contactLink . ".";
            }

            // Contrairemment au formulaire pour ajouter un véhicule, edit-vehicle-modal n'utilise pas AJAX
            // $request->wantsJson() sera toujours faux ici! Mais je garde ce bloc de code pour assurer la cohérence entre la méthode store() et la méthode update()
            if ($request->wantsJson()) {
                return response()->json(['errors' => ['immat' => [$errorMessage]]], 422);
            }

            return Redirect::back()->withErrors(['immat' => $errorMessage])->withInput();
        }

        // Si tout est bon, on met à jour la voiture
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
