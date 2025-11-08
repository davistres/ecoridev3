<?php

namespace App\Http\Controllers;

use App\Http\Requests\RechargeRequest;
use App\Models\Covoiturage;
use App\Models\Voiture;
use App\Models\Flux;
use App\Models\Satisfaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\Confirmation;

class DashboardController extends Controller
{
    /** Dashboard utilisateur */
    public function index(Request $request): View
    {
        $user = $request->user();
        $voitures = Voiture::where('user_id', $user->user_id)->get();

        $now = \Carbon\Carbon::now();
        $twoHoursAgo = $now->copy()->subHours(2);

        // Covoit proposé par l'utilisateur
        $covoiturages = Covoiturage::with('voiture')
            ->where('user_id', $user->user_id)
            ->where('trip_completed', 0)
            ->where('cancelled', 0)
            ->whereHas('voiture') // Problème fondamental lié a comment j'ai structuré la base de donnée (qui interdit de suppr une voiture si elle est référencée dans un covoit, pour garantir l'intégrité des données pour l'historique avec ON DELETE RESTRICT)
            // Problème si les covoits d'une voiture n'ont jamais été choisis (ou suppr avant), normalement, il n'y a pas de raison de garder tout cela (les covoits et la voiture) dans la base de donnée... Car on n'a pas besoin de réellement tout lister dans l'historique... L"historique étant là surout pour lister les cvoits qui ont été choisis.
            // Pour empêcher l'erreur "erreur Attempt to read property "brand" on null" d'apparaitre, j'ai utilisé whereHas('voiture') pour ne récupérer que les covoits qui ont une voiture associée.
            // Je ne sais pas quoi faire!!!! Ce système de soft delete m'arange bien et permet aussi potentiellement de créer des historiques plus complexe (d'afficher les covoit annulés car la voiture a été supprimée par ex), mais en même temps, cela complique la logique et peut créer des bugs (comme celui que j'ai eu).
            // PROBLEME POTENTIEL: Si quelqu'un n'est plus chauffeur, mais qu'il le redevient! Peux-il réenregistrer la même voiture? Normalement non, car le numéro de plaque est unique?
            // Solution possible: Lors de la suppr d'une voiture, si elle a des covoits associés, on peut juste la dissocier de l'utilisateur (mettre user_id à null) et la garder en base de donnée pour l'historique. Mais dans ce cas, il faut aussi gérer le cas où un utilisateur réenregistre une voiture qu'il avait déjà enregistrée avant (et qui est dissociée de lui mais toujours en base de donnée). Dans ce cas, on pourrait permettre la réassociation de la voiture à l'utilisateur si le numéro de plaque correspond (en mettant à jour le user_id).
            // mais il faut s'assurer que cela ne crée pas de conflits ou des incohérences dans la base de donnée.
            // TODO: Creuser tout ça et trancher!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            ->orderBy('departure_date', 'asc')
            ->orderBy('departure_time', 'asc')
            ->get()
            ->filter(function ($covoiturage) use ($twoHoursAgo) {
                $departureDateTime = \Carbon\Carbon::parse($covoiturage->departure_date . ' ' . $covoiturage->departure_time);

                if ($departureDateTime->lt($twoHoursAgo) && !$covoiturage->trip_started) {
                    return false;
                }

                $oneHourBeforeDeparture = $departureDateTime->copy()->subHour();
                if (\Carbon\Carbon::now()->gte($oneHourBeforeDeparture)) {
                    $hasConfirmedPassengers = $covoiturage->confirmations()
                        ->where('statut', 'En cours')
                        ->exists();

                    if (!$hasConfirmedPassengers) {
                        return false;
                    }
                }

                return true;
            });

        // Covoit réservé
        $reservations = Confirmation::with(['covoiturage.user', 'covoiturage.voiture'])
            ->where('user_id', $user->user_id)
            ->where('statut', 'En cours')
            ->whereHas('covoiturage', function ($q) use ($twoHoursAgo) {
                $q->where('cancelled', 0);
            })
            ->get()
            ->unique('covoit_id')
            ->filter(function ($reservation) use ($twoHoursAgo, $user) {
                $covoiturage = $reservation->covoiturage;
                $departureDateTime = \Carbon\Carbon::parse($covoiturage->departure_date . ' ' . $covoiturage->departure_time);

                if ($departureDateTime->lt($twoHoursAgo) && !$covoiturage->trip_started) {
                    return false;
                }

                if ($covoiturage->trip_completed) {
                    $hasPendingSatisfaction = Satisfaction::where('user_id', $user->user_id)
                        ->where('covoit_id', $covoiturage->covoit_id)
                        ->whereNull('date')
                        ->exists();

                    return $hasPendingSatisfaction;
                }

                return $departureDateTime->gte($twoHoursAgo) || $covoiturage->trip_started;
            });

        $pendingSatisfactions = Satisfaction::with('covoiturage.user')
            ->where('user_id', $user->user_id)
            ->whereNull('date')
            ->get();

        return view('dashboard', [
            'user' => $user,
            'voitures' => $voitures,
            'covoiturages' => $covoiturages,
            'reservations' => $reservations,
            'pendingSatisfactions' => $pendingSatisfactions,
        ]);
    }



    public function recharge(RechargeRequest $request): JsonResponse
    {
        $user = Auth::user();
        $montantInit = $user->n_credit;
        $montant = (int) $request->input('amount');

        $user->n_credit += $montant;
        $user->save();

        // Enregistre l'achat de crédit dans la table FLUX
        Flux::createAchatCredit($user->user_id, $montantInit, $montant);

        return response()->json([
            'success' => true,
            'new_balance' => $user->n_credit
        ]);
    }

    public function getTodayTrips(): JsonResponse
    {
        $user = Auth::user();
        $today = \Carbon\Carbon::today();
        $now = \Carbon\Carbon::now();
        $twoHoursAgo = $now->copy()->subHours(2);
        $trips = [];

        $covoituragesAsDriver = Covoiturage::with('voiture')
            ->where('user_id', $user->user_id)
            ->where('trip_completed', 0)
            ->where('cancelled', 0)
            ->whereHas('voiture')
            ->whereDate('departure_date', $today)
            ->get()
            ->filter(function ($covoiturage) use ($twoHoursAgo) {
                $departureDateTime = \Carbon\Carbon::parse($covoiturage->departure_date . ' ' . $covoiturage->departure_time);

                if ($departureDateTime->lt($twoHoursAgo) && !$covoiturage->trip_started) {
                    return false;
                }

                $oneHourBeforeDeparture = $departureDateTime->copy()->subHour();
                if (\Carbon\Carbon::now()->gte($oneHourBeforeDeparture)) {
                    $hasConfirmedPassengers = $covoiturage->confirmations()
                        ->where('statut', 'En cours')
                        ->exists();

                    if (!$hasConfirmedPassengers) {
                        return false;
                    }
                }

                return true;
            });

        foreach ($covoituragesAsDriver as $covoiturage) {
            $trips[] = [
                'id' => $covoiturage->covoit_id,
                'departure_date' => $covoiturage->departure_date,
                'departure_time' => $covoiturage->departure_time,
                'city_dep' => $covoiturage->city_dep,
                'city_arr' => $covoiturage->city_arr,
                'is_driver' => true,
                'trip_started' => (bool) $covoiturage->trip_started,
            ];
        }

        $reservations = Confirmation::with(['covoiturage.user', 'covoiturage.voiture'])
            ->where('user_id', $user->user_id)
            ->where('statut', 'En cours')
            ->whereHas('covoiturage', function ($q) use ($today) {
                $q->where('trip_completed', 0)
                    ->where('cancelled', 0)
                    ->whereDate('departure_date', $today);
            })
            ->get()
            ->unique('covoit_id')
            ->filter(function ($reservation) use ($twoHoursAgo) {
                $covoiturage = $reservation->covoiturage;
                $departureDateTime = \Carbon\Carbon::parse($covoiturage->departure_date . ' ' . $covoiturage->departure_time);

                if ($departureDateTime->lt($twoHoursAgo) && !$covoiturage->trip_started) {
                    return false;
                }

                return $departureDateTime->gte($twoHoursAgo) || $covoiturage->trip_started;
            });

        foreach ($reservations as $reservation) {
            $covoiturage = $reservation->covoiturage;
            $trips[] = [
                'id' => $covoiturage->covoit_id,
                'departure_date' => $covoiturage->departure_date,
                'departure_time' => $covoiturage->departure_time,
                'city_dep' => $covoiturage->city_dep,
                'city_arr' => $covoiturage->city_arr,
                'is_driver' => false,
                'trip_started' => (bool) $covoiturage->trip_started,
                'driver_name' => $covoiturage->user->name,
            ];
        }

        return response()->json($trips);
    }
}
