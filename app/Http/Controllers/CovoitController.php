<?php

namespace App\Http\Controllers;

use App\Http\Requests\DemandeRechercheCovoit;
use Illuminate\Http\Request;

class CovoitController extends Controller
{
    public function index()
    {
        return view('covoiturage');
    }

    public function search(DemandeRechercheCovoit $request)
    {
        $validated = $request->validated();

        // Pour le moment => données validées.
        // TODO => la recherche en base de données.
        dd($validated);
    }
}
