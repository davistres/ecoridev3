<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Satisfaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AvisController extends Controller
{
    /** Récupére les avis et la note moyenne d'un conducteur */
    public function getReviews(string $driverId): JsonResponse
    {
        try {
            $driver = User::find($driverId);

            if (!$driver) {
                return response()->json(['error' => 'Conducteur non trouvé.'], 404);
            }

            // => id des covoit
            $covoiturageIds = $driver->covoiturages()->pluck('covoit_id');

            // => avis pour ces covoit
            $reviews = Satisfaction::whereIn('covoit_id', $covoiturageIds)
                ->with('user:user_id,name,photo,phototype') // On charge aussi le nom et la photo de l'utilisateur qui a laissé l'avis => pour les afficher dans "Avis sur le conducteur"
                ->latest('date') // On trie par la date la plus récente
                ->get();

            $data = [
                'reviews' => $reviews,
                'average_rating' => $driver->averageRating(),
                'total_ratings' => $driver->totalRatings(),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Impossible de récupérer les avis.'], 500);
        }
    }
}