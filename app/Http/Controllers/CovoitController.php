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
    // Trouver et extraire le code postal d'une adresse (au format 12345 ou 12 345)
    private function extractPostalCode($address)
    {
        if (empty($address)) {
            return null;
        }

        // Recherche un code postal à 5 chiffres (avec ou sans espace)
        if (preg_match('/\b(\d{2}\s?\d{3})\b/', $address, $matches)) {
            // Supprime l'espace (si besoin)
            return str_replace(' ', '', $matches[1]);
        }

        return null;
    }

    // Page de recherche de covoiturage
    public function index(DemandeRechercheCovoit $request)
    {
        $covoiturages = collect();
        $searchPerformed = false;
        $errors = [];

        if (!$request->hasAny(['departure', 'arrival', 'date', 'seats'])) {
            $this->clearAllSearchSessions();
        }

        if ($request->has('departure') || $request->has('arrival') || $request->has('date') || $request->has('seats')) {
            $searchPerformed = true;
            $data = $request->validated();
            $this->clearAllSearchSessions();

            $departurePostalCode = $this->extractPostalCode($data['departure']);
            $arrivalPostalCode = $this->extractPostalCode($data['arrival']);

            if (empty($data['departure']) || !$departurePostalCode) {
                $errors[] = 'L\'adresse de départ doit contenir un code postal valide (format: 12345 ou 12 345).';
            }
            if (empty($data['arrival']) || !$arrivalPostalCode) {
                $errors[] = 'L\'adresse d\'arrivée doit contenir un code postal valide (format: 12345 ou 12 345).';
            }

            if (empty($errors)) {
                session([
                    'lieu_depart' => $data['departure'],
                    'lieu_arrivee' => $data['arrival'],
                    'date_recherche' => $data['date'],
                    'requested_seats' => $data['seats'] ?? 1
                ]);

                // 1. Vérif codes postaux
                $existingTripsBetweenCities = $this->checkExistingTrips($departurePostalCode, $arrivalPostalCode);

                if (!$existingTripsBetweenCities) {
                    session(['no_trips_between_cities' => true]);
                } else {
                    // 2. Vérif date et place
                    $query = Covoiturage::query()
                        ->where('postal_code_dep', str_replace(' ', '', $departurePostalCode))
                        ->where('postal_code_arr', str_replace(' ', '', $arrivalPostalCode))
                        ->where('cancelled', 0)
                        ->where('departure_date', '>=', now()->toDateString());

                    // A la date demandée
                    $tripsOnDate = (clone $query)->whereDate('departure_date', $data['date'])->with(['voiture', 'user'])->orderBy('departure_time')->get();
                    foreach ($tripsOnDate as $covoiturage) {
                        $covoiturage->user->average_rating = $covoiturage->user->averageRating();
                        $covoiturage->user->total_ratings = $covoiturage->user->totalRatings();
                    }

                    // Trajets parfaits (date et place)
                    $perfectMatchTrips = $tripsOnDate->filter(function ($covoiturage) use ($data) {
                        return $covoiturage->n_tickets >= ($data['seats'] ?? 1);
                    });

                    if ($perfectMatchTrips->isNotEmpty()) {
                        // 1: Le cas idéal
                        $covoiturages = $perfectMatchTrips;
                    } else {
                        // sinon => alternatives?
                        $requestedSeats = $data['seats'] ?? 1;

                        if ($tripsOnDate->isNotEmpty()) {
                            // 2: date OK mais n_place NON
                            $totalSeatsOnDate = $tripsOnDate->sum('n_tickets');

                            if ($totalSeatsOnDate >= $requestedSeats) {
                                // Place cumulable le même jour
                                session([
                                    'insufficient_seats_cumulative' => true,
                                    'trips_today' => $tripsOnDate,
                                    'total_seats_today' => $totalSeatsOnDate,
                                ]);
                                $covoiturages = $tripsOnDate;
                            } else {
                                // cumul impossible => chercher des alternatives proches (J-2, J-1, J+1, J+2)
                                $nearbyAlternatives = $this->findNearbySeatsAlternatives($departurePostalCode, $arrivalPostalCode, $data['date'], $requestedSeats);

                                if (!empty($nearbyAlternatives) && (array_sum(array_column($nearbyAlternatives, 'total_seats')) + $totalSeatsOnDate) >= $requestedSeats) {
                                    // Alternatives proches trouvées avec places cumulables suffisantes
                                    session([
                                        'insufficient_seats_alternatives' => true,
                                        'trips_today' => $tripsOnDate,
                                        'total_seats_today' => $totalSeatsOnDate,
                                        'seat_alternatives' => $nearbyAlternatives,
                                    ]);
                                    $covoiturages = $tripsOnDate;
                                } else {
                                    // Aucune alternative proche => chercher des trajets parfaits à + ou -10 jours
                                    $distantPerfectMatches = $this->findDistantPerfectMatches($departurePostalCode, $arrivalPostalCode, $data['date'], $requestedSeats);
                                    if (!empty($distantPerfectMatches)) {
                                        session([
                                            'distant_perfect_matches' => true,
                                            'perfect_matches' => $distantPerfectMatches,
                                            'trips_today' => $tripsOnDate,
                                        ]);
                                        $covoiturages = $tripsOnDate;
                                    } else {
                                        // Si aucune alternative n'est trouvée
                                        session(['general_criteria_mismatch' => true]);
                                    }
                                }
                            }
                        } else {
                            // 3: n place OK mais date NON
                            $suggestions = $this->findSuggestions($departurePostalCode, $arrivalPostalCode, $data['date'], $requestedSeats);
                            if (!empty($suggestions)) {
                                // Suggestions trouvées dans les 7 jours avant/après
                                session(['suggestions' => $suggestions]);
                            } else {
                                $distantDates = $this->findDistantDatesWithSeats($departurePostalCode, $arrivalPostalCode, $data['date'], $requestedSeats);
                                if (!empty($distantDates['closest_before']) || !empty($distantDates['closest_after'])) {
                                    // Dates plus éloignées avec places
                                    session(['distant_dates' => $distantDates]);
                                } else {
                                    // Si aucune alternative n'est trouvée
                                    session(['general_criteria_mismatch' => true]);
                                }
                            }
                        }
                    }
                }
            }
        }

        // Calcule pour les filtres (si résultats)
        $filterData = [];
        if ($covoiturages->isNotEmpty()) {
            $filterData = $this->calculateFilterData($covoiturages);
        }

        return view('covoiturage', array_merge([
            'covoiturages' => $covoiturages,
            'searchPerformed' => $searchPerformed,
            'input' => $request->all(),
            'errors' => $errors
        ], $filterData));
    }

    // FILTRES => calcul
    private function calculateFilterData($covoiturages)
    {
        if ($covoiturages->isEmpty()) {
            return [];
        }

        // Prix min/max
        $prices = $covoiturages->pluck('price')->filter()->values();
        $minPrice = $prices->min() ?? 0;
        $maxPrice = $prices->max() ?? 100;

        // Durée min/max (en minutes)
        $durations = $covoiturages->map(function ($covoiturage) {
            return $this->timeToMinutes($covoiturage->max_travel_time ?? 120);
        })->filter()->values();

        $minDuration = $durations->min() ?? 30;
        $maxDuration = $durations->max() ?? 480;

        return [
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'min_duration' => $minDuration,
            'max_duration' => $maxDuration,
            'min_duration_formatted' => $this->formatDuration($minDuration),
            'max_duration_formatted' => $this->formatDuration($maxDuration),
        ];
    }

    // Convert en mn
    private function timeToMinutes($timeString)
    {
        if (!$timeString) return 120;

        // Si c'est déjà un nombre, le retourner tel quel
        if (is_numeric($timeString)) {
            return (int) $timeString;
        }

        // Format HH:MM:SS ou HH:MM
        $parts = explode(':', $timeString);
        if (count($parts) >= 2) {
            $hours = (int) ($parts[0] ?? 0);
            $minutes = (int) ($parts[1] ?? 0);
            $seconds = count($parts) > 2 ? (int) ($parts[2] ?? 0) : 0;

            return ceil($hours * 60 + $minutes + $seconds / 60);
        }

        return 120;
    }

    // Format heure et mn
    private function formatDuration($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return $hours . 'h' . ($mins > 0 ? ' ' . $mins . 'min' : '');
        } else {
            return $mins . 'min';
        }
    }

    // Suggestions de date alternatives (plus complexe)
    private function findSuggestions($departurePostalCode, $arrivalPostalCode, $searchDate, $requestedSeats)
    {
        $dateRecherche = new \DateTime($searchDate);
        $dateMoins7 = (clone $dateRecherche)->modify('-7 days')->format('Y-m-d');
        $datePlus7 = (clone $dateRecherche)->modify('+7 days')->format('Y-m-d');

        $query = Covoiturage::where('postal_code_dep', str_replace(' ', '', $departurePostalCode))
            ->where('postal_code_arr', str_replace(' ', '', $arrivalPostalCode))
            ->where('n_tickets', '>=', $requestedSeats)
            ->where('cancelled', 0)
            ->where('departure_date', '>=', now()->toDateString());

        // Recherche dans les 7 jours avant/après (avec limite de 8*2)
        $covoituragesAvant = (clone $query)
            ->where('departure_date', '<', $searchDate)
            ->where('departure_date', '>=', $dateMoins7)
            ->orderBy('departure_date', 'desc')
            ->limit(8) // Max 8 avant
            ->get();

        $covoituragesApres = (clone $query)
            ->where('departure_date', '>', $searchDate)
            ->where('departure_date', '<=', $datePlus7)
            ->orderBy('departure_date', 'asc')
            ->limit(8) // Max 8 après
            ->get();

        if ($covoituragesAvant->isEmpty() && $covoituragesApres->isEmpty()) {
            return [];
        }

        $dateGroups = [];

        // Grouper les dates et compter le n de trajets par date
        // Covoit avant la date recherchée
        foreach ($covoituragesAvant as $covoit) {
            $date = $covoit->departure_date;
            $formattedDate = date('d/m/Y', strtotime($date));
            $diff = 'J-' . $dateRecherche->diff(new \DateTime($date))->days;

            if (!isset($dateGroups[$date])) {
                $dateGroups[$date] = [
                    'date' => $date,
                    'formatted_date' => $formattedDate,
                    'relative_day' => $diff,
                    'count' => 1
                ];
            } else {
                $dateGroups[$date]['count']++;
            }
        }

        // Grouper les dates et compter le n de trajets par date
        // Covoit après la date
        foreach ($covoituragesApres as $covoit) {
            $date = $covoit->departure_date;
            $formattedDate = date('d/m/Y', strtotime($date));
            $diff = 'J+' . $dateRecherche->diff(new \DateTime($date))->days;

            if (!isset($dateGroups[$date])) {
                $dateGroups[$date] = [
                    'date' => $date,
                    'formatted_date' => $formattedDate,
                    'relative_day' => $diff,
                    'count' => 1
                ];
            } else {
                $dateGroups[$date]['count']++;
            }
        }

        return array_values($dateGroups);
    }

    // Recherche des dates les plus proches MAIS après 7 jours
    private function findDistantDatesWithSeats($departurePostalCode, $arrivalPostalCode, $searchDate, $requestedSeats)
    {
        $dateRecherche = new \DateTime($searchDate);
        $dateMoins7 = (clone $dateRecherche)->modify('-7 days')->format('Y-m-d');
        $datePlus7 = (clone $dateRecherche)->modify('+7 days')->format('Y-m-d');

        $query = Covoiturage::where('postal_code_dep', str_replace(' ', '', $departurePostalCode))
            ->where('postal_code_arr', str_replace(' ', '', $arrivalPostalCode))
            ->where('n_tickets', '>=', $requestedSeats)
            ->where('cancelled', 0)
            ->where('departure_date', '>=', now()->toDateString());

        // Date la plus proche après 7 jours en moins
        $closestBefore = (clone $query)
            ->where('departure_date', '<', $dateMoins7)
            ->orderBy('departure_date', 'desc')
            ->first();

        // Date la plus proche APRÈS 7 jours en plus
        $closestAfter = (clone $query)
            ->where('departure_date', '>', $datePlus7)
            ->orderBy('departure_date', 'asc')
            ->first();

        return [
            'closest_before' => $closestBefore ? [
                'date' => $closestBefore->departure_date,
                'formatted_date' => date('d/m/Y', strtotime($closestBefore->departure_date))
            ] : null,
            'closest_after' => $closestAfter ? [
                'date' => $closestAfter->departure_date,
                'formatted_date' => date('d/m/Y', strtotime($closestAfter->departure_date))
            ] : null
        ];
    }

    // checkExistingTrips => ckeck si il existe des trajets entre 2 codes postaux (à n'importe quelle date)
    private function checkExistingTrips($departurePostalCode, $arrivalPostalCode)
    {
        return Covoiturage::where('postal_code_dep', str_replace(' ', '', $departurePostalCode))
            ->where('postal_code_arr', str_replace(' ', '', $arrivalPostalCode))
            ->where('cancelled', 0)
            ->where('departure_date', '>=', now()->toDateString()) // bien entendu => que les trajets futurs
            ->exists();
    }

    // Efface la session de recherche (AJAX)
    public function clearSearchSessions()
    {
        session()->forget([
            'suggestions',
            'distant_dates',
            'no_trips_between_cities',
            'insufficient_seats_cumulative',
            'insufficient_seats_alternatives',
            'distant_perfect_matches',
            'general_criteria_mismatch',
            'perfect_matches',
            'trips_today',
            'requested_seats',
            'total_seats_today',
            'seat_alternatives',
            'lieu_depart',
            'lieu_arrivee',
            'date_recherche'
        ]);
        return response()->json(['success' => true]);
    }

    // Efface toutes les sessions de recherche (au chargement initial)
    private function clearAllSearchSessions()
    {
        session()->forget([
            'suggestions',
            'distant_dates',
            'no_trips_between_cities',
            'insufficient_seats_cumulative',
            'insufficient_seats_alternatives',
            'distant_perfect_matches',
            'general_criteria_mismatch',
            'perfect_matches',
            'trips_today',
            'requested_seats',
            'total_seats_today',
            'seat_alternatives',
            'lieu_depart',
            'lieu_arrivee',
            'date_recherche'
        ]);
    }

    // Alternative de place pour J-2, J-1, J+1, J+2
    private function findNearbySeatsAlternatives($departurePostalCode, $arrivalPostalCode, $searchDate, $requestedSeats)
    {
        $dateRecherche = new \DateTime($searchDate);
        $alternatives = [];

        $daysToCheck = [-2, -1, 1, 2];

        foreach ($daysToCheck as $dayOffset) {
            $checkDate = (clone $dateRecherche)->modify("{$dayOffset} days")->format('Y-m-d');

            // Pas la peine de vérifier les dates passées
            if ($checkDate < now()->toDateString()) {
                continue;
            }

            $totalSeats = Covoiturage::where('postal_code_dep', str_replace(' ', '', $departurePostalCode))
                ->where('postal_code_arr', str_replace(' ', '', $arrivalPostalCode))
                ->whereDate('departure_date', $checkDate)
                ->where('cancelled', 0)
                ->sum('n_tickets');

            if ($totalSeats > 0) {
                $relativeDay = $dayOffset > 0 ? "J+{$dayOffset}" : "J{$dayOffset}";
                $formattedDate = date('d/m/Y', strtotime($checkDate));

                $alternatives[] = [
                    'relative_day' => $relativeDay,
                    'formatted_date' => $formattedDate,
                    'total_seats' => $totalSeats,
                    'date' => $checkDate
                ];
            }
        }

        return $alternatives;
    }

    // Trajets parfaits dans les 10 jours (avant/après)
    private function findDistantPerfectMatches($departurePostalCode, $arrivalPostalCode, $searchDate, $requestedSeats)
    {
        $dateRecherche = new \DateTime($searchDate);
        $matches = [];

        // Jusqu'à -10
        $closestBefore = Covoiturage::where('postal_code_dep', str_replace(' ', '', $departurePostalCode))
            ->where('postal_code_arr', str_replace(' ', '', $arrivalPostalCode))
            ->where('departure_date', '<', $searchDate)
            ->where('departure_date', '>=', (clone $dateRecherche)->modify('-10 days')->format('Y-m-d'))
            ->where('n_tickets', '>=', $requestedSeats)
            ->where('cancelled', 0)
            ->where('departure_date', '>=', now()->toDateString())
            ->orderBy('departure_date', 'desc')
            ->first();

        // Jusqu'à +10
        $closestAfter = Covoiturage::where('postal_code_dep', str_replace(' ', '', $departurePostalCode))
            ->where('postal_code_arr', str_replace(' ', '', $arrivalPostalCode))
            ->where('departure_date', '>', $searchDate)
            ->where('departure_date', '<=', (clone $dateRecherche)->modify('+10 days')->format('Y-m-d'))
            ->where('n_tickets', '>=', $requestedSeats)
            ->where('cancelled', 0)
            ->where('departure_date', '>=', now()->toDateString())
            ->orderBy('departure_date', 'asc')
            ->first();

        if ($closestBefore) {
            $matches['before'] = [
                'date' => $closestBefore->departure_date,
                'formatted_date' => date('d/m/Y', strtotime($closestBefore->departure_date)),
                'seats' => $closestBefore->n_tickets
            ];
        }

        if ($closestAfter) {
            $matches['after'] = [
                'date' => $closestAfter->departure_date,
                'formatted_date' => date('d/m/Y', strtotime($closestAfter->departure_date)),
                'seats' => $closestAfter->n_tickets
            ];
        }

        return $matches;
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
