<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Satisfaction;
use App\Models\Covoiturage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AvisController extends Controller
{
    /** Récupére les avis et la note moyenne d'un conducteur */
    public function getReviews(string $driverId): JsonResponse
    {
        Log::info("Début de getReviews pour le conducteur ID: {$driverId}");

        try {
            $driver = User::find($driverId);
            if (!$driver) {
                Log::warning("Conducteur non trouvé pour l'ID: {$driverId}");
                return response()->json(['error' => 'Conducteur non trouvé.'], 404);
            }
            Log::info("Conducteur trouvé: {$driver->name}");

            // Récupére tous les covoit d'un conducteur
            $covoiturageIds = Covoiturage::where('user_id', $driverId)
                ->pluck('covoit_id')
                ->toArray();

            Log::info("Nombre de covoiturages du conducteur: " . count($covoiturageIds));

            // Récupére tous les avis de ses covoit
            $reviews = Satisfaction::whereIn('covoit_id', $covoiturageIds)
                ->whereNotNull('note')
                ->with('user:user_id,name')
                ->orderBy('date', 'desc')
                ->get();

            Log::info(count($reviews) . " avis trouvés pour le conducteur ID: {$driverId}");

            // Affichage des avis
            $formattedReviews = $reviews->map(function ($review) {
                return [
                    'review' => $review->review,
                    'comment' => $review->comment,
                    'note' => (int) $review->note,
                    'date' => $review->date->format('Y-m-d'),
                    'user' => [
                        'name' => $review->user ? $review->user->name : 'Anonyme',
                    ],
                ];
            });

            // Calcule la moy et le n total d'avis
            $avgRating = $driver->averageRating();
            $totalRatings = $driver->totalRatings();

            Log::info("Note moyenne: {$avgRating}, Nombre total de notes: {$totalRatings}");

            $data = [
                'reviews' => $formattedReviews,
                'average_rating' => $avgRating ? round($avgRating, 1) : 0,
                'total_ratings' => $totalRatings,
            ];

            Log::info("Données formatées prêtes à être envoyées.");

            // S'assurer que la réponse est en UTF-8!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            // Trés important!!!! J'ai eu beaucoup de probléme à cause de ça!!!!!
            return response()->json($data, 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            Log::error("Erreur dans getReviews pour le conducteur ID: {$driverId} - " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Impossible de récupérer les avis.'], 500);
        }
    }
}
