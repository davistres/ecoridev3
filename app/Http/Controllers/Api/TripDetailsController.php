<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Covoiturage;
use App\Models\Satisfaction;
use Illuminate\Http\JsonResponse;

class TripDetailsController extends Controller
{
    // Récupére les infos complets sur un covoit
    public function getDetails($id): JsonResponse
    {
        try {
            $covoiturage = Covoiturage::with(['user', 'voiture'])
                ->where('covoit_id', $id)
                ->first();

            if (!$covoiturage) {
                return response()->json(['error' => 'Covoiturage non trouvé'], 404);
            }

            // Récupére les avis (du conducteur du covoit)
            $reviews = Satisfaction::whereHas('covoiturage', function ($query) use ($covoiturage) {
                $query->where('user_id', $covoiturage->user_id);
            })
                ->whereNotNull('review')
                ->whereNotNull('note')
                ->with('user:user_id,name')
                ->orderBy('date', 'desc')
                ->get();

            // Calcule les places restantes
            $placesRestantes = $covoiturage->n_tickets; // TODO: créer une logique pour déduire les places réservées!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

            // Les infos à afficher
            $data = [
                'covoit_id' => $covoiturage->covoit_id,
                'city_dep' => $covoiturage->city_dep,
                'city_arr' => $covoiturage->city_arr,
                'departure_address' => $covoiturage->departure_address,
                'add_dep_address' => $covoiturage->add_dep_address,
                'postal_code_dep' => $covoiturage->postal_code_dep,
                'arrival_address' => $covoiturage->arrival_address,
                'add_arr_address' => $covoiturage->add_arr_address,
                'postal_code_arr' => $covoiturage->postal_code_arr,
                'departure_date' => $covoiturage->departure_date,
                'arrival_date' => $covoiturage->arrival_date,
                'departure_time' => $covoiturage->departure_time,
                'arrival_time' => $covoiturage->arrival_time,
                'max_travel_time' => $covoiturage->max_travel_time,
                'price' => $covoiturage->price,
                'n_tickets' => $covoiturage->n_tickets,
                'places_restantes' => $placesRestantes,
                'eco_travel' => $covoiturage->eco_travel,

                // Info conducteur
                'driver' => [
                    'name' => $covoiturage->user->name,
                    'average_rating' => $covoiturage->user->averageRating(),
                    'total_ratings' => $covoiturage->user->totalRatings(),
                    'pref_smoke' => $covoiturage->user->pref_smoke,
                    'pref_pet' => $covoiturage->user->pref_pet,
                    'pref_libre' => $covoiturage->user->pref_libre,
                    'photo' => $covoiturage->user->photo,
                    'phototype' => $covoiturage->user->phototype,
                ],

                // Info du véhicule
                'voiture' => $covoiturage->voiture ? [
                    'immat' => $covoiturage->voiture->immat,
                    'brand' => $covoiturage->voiture->brand,
                    'model' => $covoiturage->voiture->model,
                    'color' => $covoiturage->voiture->color,
                    'energie' => $covoiturage->voiture->energie,
                    'n_place' => $covoiturage->voiture->n_place,
                ] : null,

                // Avis
                'reviews' => $reviews->map(function ($review) {
                    return [
                        'satisfaction_id' => $review->satisfaction_id,
                        'user_id' => $review->user_id,
                        'note' => $review->note,
                        'review' => $review->review,
                        'date' => $review->date,
                        'feeling' => $review->feeling,
                        'utilisateur' => [
                            'user_id' => $review->user->user_id,
                            'name' => $review->user->name,
                        ]
                    ];
                })
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des détails'], 500);
        }
    }

    // Le statut de l'utilisateur par rapport au covoit?
    public function getUserStatus($tripId): JsonResponse
    {
        try {
            // TODO: plus tard, je devrai faire en sorte que le bouton "particper" change selon le statut de l'utilisateur
            // Pour l'instant, on retourne des données par défaut

            $data = [
                'can_participate' => true,
                'button_text' => 'Participer',
                'redirect_to' => route('covoiturage')
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la vérification du statut'], 500);
        }
    }
}
