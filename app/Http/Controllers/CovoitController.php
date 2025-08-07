<?php

namespace App\Http\Controllers;

use App\Http\Requests\DemandeRechercheCovoit;
use Illuminate\Http\Request;

class CovoitController extends Controller
{
    public function index()
    {
        // Exemple pour voir le rendu
        $covoiturages = [
            [
                'id' => 1,
                'lieu_depart' => 'Paris',
                'lieu_arrivee' => 'Lyon',
                'date_depart' => '2025-09-15',
                'heure_depart' => '08:00:00',
                'heure_arrivee' => '12:00:00',
                'prix' => 25,
                'places_restantes' => 3,
                'ecologique' => true,
                'pseudo_chauffeur' => 'Jean Dupont',
                'note_chauffeur' => 4.8,
                'photo_chauffeur_data' => null,
                'max_travel_time' => '04:00:00',
            ],
            [
                'id' => 2,
                'lieu_depart' => 'Marseille',
                'lieu_arrivee' => 'Nice',
                'date_depart' => '2025-09-16',
                'heure_depart' => '10:00:00',
                'heure_arrivee' => '12:30:00',
                'prix' => 15,
                'places_restantes' => 2,
                'ecologique' => false,
                'pseudo_chauffeur' => 'Marie Curie',
                'note_chauffeur' => 4.9,
                'photo_chauffeur_data' => null,
                'max_travel_time' => '02:30:00',
            ],
            [
                'id' => 3,
                'lieu_depart' => 'Lille',
                'lieu_arrivee' => 'Bruxelles',
                'date_depart' => '2025-09-17',
                'heure_depart' => '14:00:00',
                'heure_arrivee' => '15:30:00',
                'prix' => 10,
                'places_restantes' => 1,
                'ecologique' => true,
                'pseudo_chauffeur' => 'Pierre Martin',
                'note_chauffeur' => 4.5,
                'photo_chauffeur_data' => null,
                'max_travel_time' => '01:30:00',
            ],
        ];

        return view('covoiturage', ['covoiturages' => $covoiturages]);
    }

    public function search(DemandeRechercheCovoit $request)
    {
        $validated = $request->validated();

        // Pour le moment => données validées.
        // TODO => la recherche en base de données.
        dd($validated);
    }
}
