<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ModifCovoitRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Récupère le covoit à partir de la route
        $covoiturage = $this->route('covoiturage');

        // La persnne connecté doit être le propriétaire du covoit pour le modifier
        return $covoiturage && $covoiturage->user_id == Auth::id();
    }

    // Régles de validation
    public function rules(): array
    {
        return [
            'departure_address' => ['required', 'string', 'max:120'],
            'add_dep_address' => ['nullable', 'string', 'max:120'],
            'postal_code_dep' => ['required', 'string', 'max:6'],
            'city_dep' => ['required', 'string', 'max:45'],
            'arrival_address' => ['required', 'string', 'max:120'],
            'add_arr_address' => ['nullable', 'string', 'max:120'],
            'postal_code_arr' => ['required', 'string', 'max:6'],
            'city_arr' => ['required', 'string', 'max:45'],
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'departure_time' => ['required', 'date_format:H:i'],
            'arrival_date' => ['required', 'date', 'after_or_equal:departure_date'],
            'arrival_time' => ['required', 'date_format:H:i'],
            'max_travel_time' => ['required', 'date_format:H:i'],
            'voiture_id' => [
                'required',
                'integer',
                Rule::exists('voiture', 'voiture_id')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
            'n_tickets' => ['required', 'integer', 'min:1', function ($attribute, $value, $fail) {
                $voiture = Auth::user()->voitures()->find($this->input('voiture_id'));
                if ($voiture && $value > $voiture->n_place) {
                    $fail("Le nombre de places ne peut pas dépasser celui du véhicule sélectionné ({$voiture->n_place} places).");
                }
            }],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }
}