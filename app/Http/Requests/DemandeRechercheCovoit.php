<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemandeRechercheCovoit extends FormRequest
{
    /** Tout le monde peut faire une recherche... D'après ce que j'ai lu, faut mettre ça!!!! Mais je ne sais pas pourquoi? Son utilité? A COMPRENDRE!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/
    public function authorize(): bool
    {
        return true;
    }

    /** Règles de validation */
    public function rules(): array
    {
        return [
            'departure' => 'required|string|max:255',
            'arrival' => 'required|string|max:255',
            'date' => 'required|date',
            'seats' => 'required|integer|min:1|max:8',
        ];
    }
}
