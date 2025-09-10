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
        $covoiturages = collect(); //
        $searchPerformed = false;
        $errors = [];

        // Si on a les infos, on fait la recherche
        if ($request->has('departure') || $request->has('arrival') || $request->has('date') || $request->has('seats')) {
            $searchPerformed = true;
            $data = $request->validated();

            // Extraction des codes postaux
            $departurePostalCode = null;
            $arrivalPostalCode = null;

            if (!empty($data['departure'])) {
                $departurePostalCode = $this->extractPostalCode($data['departure']);
                if (!$departurePostalCode) {
                    $errors[] = 'L\'adresse de départ doit contenir un code postal valide (format: 12345 ou 12 345).';
                }
            }

            if (!empty($data['arrival'])) {
                $arrivalPostalCode = $this->extractPostalCode($data['arrival']);
                if (!$arrivalPostalCode) {
                    $errors[] = 'L\'adresse d\'arrivée doit contenir un code postal valide (format: 12345 ou 12 345).';
                }
            }

            // Si pas d'erreurs => on effectue la recherche
            if (empty($errors)) {
                $query = Covoiturage::query();

                // Ordre => par code postal de départ
                if ($departurePostalCode) {
                    $query->where('postal_code_dep', str_replace(' ', '', $departurePostalCode));
                }

                // Ordre => par code postal d'arrivée
                if ($arrivalPostalCode) {
                    $query->where('postal_code_arr', str_replace(' ', '', $arrivalPostalCode));
                }

                // Ensuite, par date de départ (même jour)
                if (!empty($data['date'])) {
                    $query->whereDate('departure_date', $data['date']);
                }

                // Et enfin, par n de places (au minimum le nombre demandé)
                if (!empty($data['seats'])) {
                    $query->where('n_tickets', '>=', $data['seats']);
                }

                // Les covoits annulés ne sont pas affichés
                $query->where('cancelled', 0);

                // Les covoits passés ne sont pas affichés aussi
                $query->where('departure_date', '>=', now()->toDateString());

                // Relations voiture et user
                $covoiturages = $query->with('voiture', 'user')->orderBy('departure_date')->orderBy('departure_time')->get();

                // Si aucun résultat trouvé, chercher des suggestions
                if ($covoiturages->isEmpty() && $departurePostalCode && $arrivalPostalCode && !empty($data['date'])) {
                    $suggestions = $this->findSuggestions($departurePostalCode, $arrivalPostalCode, $data['date']);
                    if (!empty($suggestions)) {
                        session([
                            'suggestions' => $suggestions,
                            'lieu_depart' => $data['departure'],
                            'lieu_arrivee' => $data['arrival'],
                            'date_recherche' => $data['date']
                        ]);
                    }
                }
            }
        }

        return view('covoiturage', [
            'covoiturages' => $covoiturages,
            'searchPerformed' => $searchPerformed,
            'input' => $request->all(),
            'errors' => $errors
        ]);
    }

    // Suggestions de date alternatives
    private function findSuggestions($departurePostalCode, $arrivalPostalCode, $searchDate)
    {
        $dateRecherche = new \DateTime($searchDate);
        $dateMoins7 = (clone $dateRecherche)->modify('-7 days')->format('Y-m-d');
        $datePlus7 = (clone $dateRecherche)->modify('+7 days')->format('Y-m-d');

        // Jusqu'à -7 jours
        $covoituragesAvant = Covoiturage::where('postal_code_dep', str_replace(' ', '', $departurePostalCode))
            ->where('postal_code_arr', str_replace(' ', '', $arrivalPostalCode))
            ->where('departure_date', '<', $searchDate)
            ->where('departure_date', '>=', $dateMoins7)
            ->where('cancelled', 0)
            ->where('departure_date', '>=', now()->toDateString())
            ->orderBy('departure_date', 'desc')
            ->get();

        // Jusqu'à +7 jours
        $covoituragesApres = Covoiturage::where('postal_code_dep', str_replace(' ', '', $departurePostalCode))
            ->where('postal_code_arr', str_replace(' ', '', $arrivalPostalCode))
            ->where('departure_date', '>', $searchDate)
            ->where('departure_date', '<=', $datePlus7)
            ->where('cancelled', 0)
            ->where('departure_date', '>=', now()->toDateString())
            ->orderBy('departure_date', 'asc')
            ->get();

        // Suggestions par date
        $dateGroups = [];

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
