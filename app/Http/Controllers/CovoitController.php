<?php

namespace App\Http\Controllers;

use App\Http\Requests\DemandeRechercheCovoit;
use App\Http\Requests\StoreCovoiturageRequest;
use App\Http\Requests\ModifCovoitRequest;
use App\Models\Covoiturage;
use App\Models\Confirmation;
use App\Models\Satisfaction;
use App\Models\User;
use App\Models\Voiture;
use App\Models\Flux;
use App\Mail\SatisfactionSurveyMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;

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
                        ->where('trip_started', 0)
                        ->where('departure_date', '>=', now()->toDateString());

                    // A la date demandée
                    $tripsOnDate = (clone $query)
                        ->whereDate('departure_date', $data['date'])
                        ->with(['voiture', 'user'])
                        ->orderBy('departure_time')
                        ->get()
                        ->filter(function ($covoiturage) {
                            $departureDateTime = \Carbon\Carbon::parse($covoiturage->departure_date . ' ' . $covoiturage->departure_time);
                            $oneHourFromNow = \Carbon\Carbon::now()->addHour();

                            return $departureDateTime->gte($oneHourFromNow);
                        });

                    foreach ($tripsOnDate as $covoiturage) {
                        $covoiturage->user->average_rating = $covoiturage->user->averageRating();
                        $covoiturage->user->total_ratings = $covoiturage->user->totalRatings();
                    }

                    // Trajets parfaits (date et place dispo)
                    $perfectMatchTrips = $tripsOnDate->filter(function ($covoiturage) use ($data) {
                        return $covoiturage->hasAvailableSeats($data['seats'] ?? 1);
                    });

                    if ($perfectMatchTrips->isNotEmpty()) {
                        // 1: Le cas idéal
                        $covoiturages = $perfectMatchTrips;
                    } else {
                        // sinon => alternatives?
                        $requestedSeats = $data['seats'] ?? 1;

                        if ($tripsOnDate->isNotEmpty()) {
                            // 2: date OK mais n_place NON
                            $totalSeatsOnDate = $tripsOnDate->sum(function ($covoiturage) {
                                return $covoiturage->available_seats;
                            });

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

        // Ajouter les infos du btn à chaque covoit
        $covoiturages = $covoiturages->map(function ($covoiturage) {
            $buttonStatus = $this->getButtonStatus($covoiturage);
            $covoiturage->button_status = $buttonStatus;
            return $covoiturage;
        });

        $hasPendingSatisfaction = false;
        if (Auth::check()) {
            $hasPendingSatisfaction = Satisfaction::where('user_id', Auth::id())
                ->whereNull('date')
                ->exists();
        }

        return view('covoiturage', array_merge([
            'covoiturages' => $covoiturages,
            'searchPerformed' => $searchPerformed,
            'input' => $request->all(),
            'errors' => $errors,
            'hasPendingSatisfaction' => $hasPendingSatisfaction
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
            ->where('trip_started', 0) // Sauf les trajets complets
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
            ->where('trip_started', 0) // Sauf les trajets complets
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
            ->where('trip_started', 0) // Sauf les trajets complets
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
                ->where('trip_started', 0) // Sauf les trajets complets
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
            ->where('trip_started', 0) // Sauf les trajets complets
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
            ->where('trip_started', 0) // Sauf les trajets complets
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

    public function hasReservations(Covoiturage $covoiturage)
    {
        // Check si l'user connecté est le propriétaire du covoit
        if (Auth::id() !== $covoiturage->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $hasReservations = $covoiturage->confirmations()
            ->where('statut', 'En cours')
            ->exists();

        return response()->json($hasReservations);
    }

    // Le btn "Participer" en fonction de la situation
    public function getButtonStatus($covoiturage)
    {
        $user = auth()->user();

        // Utilisateur non connecté
        if (!$user) {
            return [
                'can_participate' => false,
                'button_text' => 'Se connecter',
                'redirect_to' => route('login'),
                'button_class' => 'bg-red-600 hover:bg-red-700'
            ];
        }

        // Utilisateur connecté mais rôle "Conducteur"
        if ($user->role === 'Conducteur') {
            return [
                'can_participate' => false,
                'button_text' => 'Changer de rôle',
                'redirect_to' => route('dashboard') . '#role-section',
                'button_class' => 'bg-red-600 hover:bg-red-700'
            ];
        }

        // On récupére le n place demandé lors de la recherche
        $requestedSeats = session('requested_seats', 1);
        $totalCost = $covoiturage->price * $requestedSeats;

        // Utilisateur connecté mais pas assez de crédits
        if ($user->n_credit < $totalCost) {
            return [
                'can_participate' => false,
                'button_text' => 'Recharger votre crédit',
                'redirect_to' => route('dashboard') . '#credits-section',
                'button_class' => 'bg-red-600 hover:bg-red-700',
                'show_credit_warning' => true,
                'user_credits' => $user->n_credit,
                'requested_seats' => $requestedSeats,
                'total_cost' => $totalCost
            ];
        }

        // Si tout est OK, on peut participer
        $confirmationUrl = '/covoiturage/' . $covoiturage->covoit_id . '/confirmation?seats=' . $requestedSeats;
        Log::info('URL de confirmation générée: ' . $confirmationUrl);

        return [
            'can_participate' => true,
            'button_text' => 'Participer',
            'redirect_to' => $confirmationUrl,
            'button_class' => 'bg-green-600 hover:bg-green-700'
        ];
    }

    // Page de confirmation
    // On confirme que l'on va participer à un covoit via un système de double confirmation => ici, c'est la 1ère avec la page de confirmation
    public function showConfirmation($id)
    {
        $user = auth()->user();
        $covoiturage = Covoiturage::where('covoit_id', $id)->first();

        if (!$covoiturage) {
            return redirect()->route('covoiturage')->with('error', 'Covoiturage non trouvé');
        }

        // L'utilisateur peut participer?
        $buttonStatus = $this->getButtonStatus($covoiturage);
        if (!$buttonStatus['can_participate']) {
            return redirect()->route('covoiturage')->with('error', 'Vous ne pouvez pas participer à ce covoiturage');
        }

        // On récupère le n place depuis la recherche (ou 1 par défaut)
        $requestedSeats = request()->input('seats') ?? session('requested_seats') ?? session('seats') ?? session('n_tickets') ?? 1;

        // On conserve les paramètres de recherche en cas de retour sur la page covoiturage
        session()->put('n_tickets', $requestedSeats);
        if (!session()->has('ville_depart')) {
            session()->put('ville_depart', $covoiturage->departure_address);
        }
        if (!session()->has('ville_arrivee')) {
            session()->put('ville_arrivee', $covoiturage->arrival_address);
        }
        if (!session()->has('date_recherche')) {
            session()->put('date_recherche', $covoiturage->departure_date);
        }

        // On récupére toutes les infos du covoit (les mêmes que celle de la modale Détails)
        $conducteur = User::find($covoiturage->user_id);
        $voiture = Voiture::find($covoiturage->voiture_id);

        // + Les avis du conducteur
        $covoiturageIds = $conducteur->covoiturages()->pluck('covoit_id');
        $avis = Satisfaction::whereIn('covoit_id', $covoiturageIds)
            ->with('user:user_id,name,photo,phototype') // On charge aussi le nom et la photo de l'utilisateur qui a laissé l'avis => pour les afficher dans "Avis sur le conducteur"
            ->latest('date') // On trie par la date la plus récente
            ->get();

        // On calcul la note moyenne
        $notesMoyenne = $conducteur->averageRating();
        $totalRatings = $conducteur->totalRatings();

        $reservedSeats = Confirmation::where('covoit_id', $id)
            ->where('statut', 'En cours')
            ->count();
        $placesRestantes = $covoiturage->n_tickets - $reservedSeats;

        return view('covoiturage-confirmation', [
            'covoiturage' => $covoiturage,
            'conducteur' => $conducteur,
            'voiture' => $voiture,
            'avis' => $avis,
            'notesMoyenne' => $notesMoyenne,
            'totalRatings' => $totalRatings,
            'placesRestantes' => $placesRestantes,
            'user' => $user
        ]);
    }

    // On finalise la participation au covoit
    public function participate(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $covoiturage = Covoiturage::findOrFail($id);
            $requestedSeats = session('n_tickets', 1); // On récupére le n place demandé de la session, ou 1 par défaut


            // Section de validation !!!!!!!!!!!!!!!!!!!!!!!!!!
            // Un conducteur ne peut pas réserver une place dans son propre trajet... Si il est juste conducteur, normalement, il ne pourrait pas (car on lui demanderait de changer de rôle)! Mais si il a le rôle les deux, je dois alors bloquer cette possibilité.
            if ($covoiturage->user_id == $user->user_id) {
                return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas réserver une place dans votre propre covoiturage.'], 400);
            }

            // Trajet valide? Disponible?
            if ($covoiturage->cancelled || $covoiturage->trip_completed) {
                return response()->json(['success' => false, 'message' => 'Ce covoiturage n\'est plus disponible.'], 400);
            }

            // L'utilisateur a-t-il déjà réservé? (=> vérif les résa actives)
            $dejaReserve = Confirmation::where('covoit_id', $id)
                ->where('user_id', $user->user_id)
                ->where('statut', 'En cours')
                ->exists();
            if ($dejaReserve) {
                return response()->json(['success' => false, 'message' => 'Vous avez déjà fait une réservation dans ce covoiturage.'], 400);
            }

            // Assez de places? (=> on compte seulement les résa actives)
            $reservedSeats = Confirmation::where('covoit_id', $id)
                ->where('statut', 'En cours')
                ->count();
            $availableSeats = $covoiturage->n_tickets - $reservedSeats;

            if ($availableSeats < $requestedSeats) {
                return response()->json(['success' => false, 'message' => 'Il n\'y a pas assez de places disponibles.'], 400);
            }

            // Assez de crédits?
            $totalCost = $covoiturage->price * $requestedSeats;
            if ($user->n_credit < $totalCost) {
                return response()->json(['success' => false, 'message' => 'Vous n\'avez pas assez de crédits pour effectuer cette réservation.'], 400);
            }


            // Pour comprendre ce qui va suivre, il faut expliquer plusieurs choses:
            // Une résa peut avoir logiquement une ou plusieurs places...
            // Au départ, je voulais créer dans la table CONFIRMATION, une entrée par réservation. Mais j'ai changé d'avis. Désormais, chaque place réservée aura une confirmation distincte dans la table CONFIRMATION (avec un n_conf différent)
            // Pourquoi? Pour simplifier la gestion des annulations partielles (si l'utilisateur réserve 3 places et qu'il veut n'en annuler qu'une seule, par exemple)... Annulation partielle que je n'ai pas encore implémentée => TODO MAIS PAS OBLIGE!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            // Cette partie est scindée en deux étapes... Dans le point 2, je crée la 1ère confirmation (n_conf=1) pour la première place réservée. Et si il y a plus que une place de réservée, je crée les autres confirmations (n_conf=2, 3, etc.) dans le point 4.
            // Pourquoi cela? Parce que, quand on a le conf_id, la priorité est de réaliser le point 3, qui est l'enregistrement des mouvements de crédit dans la table FLUX. Ensuite, on peut créer les autres confirmations.

            // Autre chose à savoir : il y a différent type de flux : « enum('reservation','part_plateforme','part_conducteur','paiement','bonus_inscription','remboursement','achat_crédit') »… Il y en a qui sont simples (comme 'bonus_inscription' et 'achat_crédit') et d’autres plus complexe. Le type 'reservation' est le parent de 'part_plateforme', 'part_conducteur' et de 'paiement'…
            // 'part_plateforme', 'part_conducteur' ne sont pas vraiment des FLUX (des mouvements de crédit)… Ce sont juste des lignes permettant de comprendre la division la somme de crédit de la 'reservation' entre la plateforme et le conducteur… Ce sont juste donc, des lignes intermédiaire afin de préparer le 'paiement'… Et donc, il y aura deux lignes aussi de 'paiement' : l’une vers la plateforme (= vers le compte crédit de l’admin) et l’autre vers le compte du conducteur.
            // TODO : créer le paiement vers le compte crédit de l’admin !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

            // Après VALIDATION:
            // 1. Enlever le prix en crédit de l'utilisateur
            $montantInit = $user->n_credit;
            $user->n_credit -= $totalCost;
            $user->save();

            // 2. Création de la 1ère conf pour récupérer le conf_id
            $firstConfirmation = Confirmation::create([
                'covoit_id' => $id,
                'user_id' => $user->user_id,
                'statut' => 'En cours',
                'n_conf' => 1
            ]);

            // 3. Enregistrer la résa et ses flux "enfants" dans la table FLUX
            Flux::createReservation(
                $firstConfirmation->conf_id,
                $user->user_id,
                $montantInit,
                $totalCost,
                $requestedSeats
            );

            // 4. Création des conf restantes (si plusieurs places)
            for ($i = 1; $i < $requestedSeats; $i++) {
                Confirmation::create([
                    'covoit_id' => $id,
                    'user_id' => $user->user_id,
                    'statut' => 'En cours',
                    'n_conf' => $i + 1
                ]);
            }

            // 5. Le covoit est_il complet maintenant?
            $newReservedSeats = $reservedSeats + $requestedSeats;
            if ($covoiturage->n_tickets - $newReservedSeats <= 0) {
                $covoiturage->trip_started = 1; // Si oui => on indique complet
                $covoiturage->save();
            }

            // Si tout est ok, on valide la transaction
            DB::commit();

            // On efface alors le n place de la session
            session()->forget(['n_tickets']);

            return response()->json([
                'success' => true,
                'message' => 'Votre participation a été confirmée avec succès !',
                'new_balance' => $user->n_credit
            ]);
        } catch (\Exception $e) {
            // Si erreur => on annule tout
            DB::rollback();
            Log::error('Erreur lors de la confirmation de participation: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Une erreur technique est survenue. Veuillez réessayer.'], 500);
        }
    }

    public function completeTripAndSendSurveys(Request $request): JsonResponse
    {
        try {
            $covoiturageId = $request->input('covoiturage_id');
            $user = Auth::user();

            $covoiturage = Covoiturage::with(['user', 'confirmations.user'])
                ->where('covoit_id', $covoiturageId)
                ->where('user_id', $user->user_id)
                ->first();

            if (!$covoiturage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Covoiturage non trouvé ou vous n\'êtes pas le conducteur.'
                ], 404);
            }

            if ($covoiturage->trip_completed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce covoiturage est déjà marqué comme terminé.'
                ], 400);
            }

            DB::beginTransaction();

            $covoiturage->trip_completed = 1;
            $covoiturage->save();

            $confirmedPassengers = $covoiturage->confirmations()
                ->where('statut', 'En cours')
                ->with('user')
                ->get();

            $uniquePassengers = $confirmedPassengers->unique('user_id')->pluck('user');

            $emailsSent = 0;
            $satisfactionsCreated = 0;

            foreach ($uniquePassengers as $passenger) {
                if ($passenger) {
                    Satisfaction::create([
                        'user_id' => $passenger->user_id,
                        'covoit_id' => $covoiturage->covoit_id,
                        'feeling' => false, // Valeur par défaut pour éviter la contrainte NOT NULL
                        'comment' => null,
                        'review' => null,
                        'note' => null,
                        'date' => now(), // Date actuelle pour éviter la contrainte NOT NULL
                    ]);
                    $satisfactionsCreated++;

                    if ($passenger->email) {
                        try {
                            Mail::to($passenger->email)->send(new SatisfactionSurveyMail(
                                $passenger->name,
                                $covoiturage->user->name,
                                $covoiturage->city_dep,
                                $covoiturage->city_arr,
                                \Carbon\Carbon::parse($covoiturage->departure_date)->format('d/m/Y')
                            ));
                            $emailsSent++;
                        } catch (\Exception $e) {
                            Log::error('Erreur lors de l\'envoi de l\'email de satisfaction à ' . $passenger->email . ': ' . $e->getMessage());
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Covoiturage terminé avec succès.',
                'emails_sent' => $emailsSent,
                'satisfactions_created' => $satisfactionsCreated
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la finalisation du covoiturage: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur technique est survenue. Veuillez réessayer.'
            ], 500);
        }
    }
}
