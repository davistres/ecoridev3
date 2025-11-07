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

    // Empêcher le chevauchement des covoits
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            $covoiturageIdToExclude = $this->route('covoiturage')->covoit_id;

            $newDepartureDateTime = \Carbon\Carbon::parse($this->input('departure_date') . ' ' . $this->input('departure_time'));
            $newArrivalDateTime = \Carbon\Carbon::parse($this->input('arrival_date') . ' ' . $this->input('arrival_time'));

            // Validation: Si départ aujourd'hui, il doit être au minimum 6h après l'heure actuelle
            $now = \Carbon\Carbon::now();
            $today = $now->toDateString();

            if ($this->input('departure_date') === $today) {
                $minimumDepartureTime = $now->copy()->addHours(6);

                if ($newDepartureDateTime->lt($minimumDepartureTime)) {
                    $validator->errors()->add(
                        'departure_time',
                        'Pour un départ aujourd\'hui, l\'heure de départ doit être au minimum à ' .
                            $minimumDepartureTime->format('H:i') .
                            ' (au moins 6 heures après l\'heure actuelle).'
                    );
                    return;
                }
            }

            $existingCovoiturages = \App\Models\Covoiturage::where('user_id', $user->user_id)
                ->where('covoit_id', '!=', $covoiturageIdToExclude)
                ->where('cancelled', 0)
                ->get();

            foreach ($existingCovoiturages as $existingCovoiturage) {
                $existingDepartureDateTime = \Carbon\Carbon::parse($existingCovoiturage->departure_date . ' ' . $existingCovoiturage->departure_time);
                $existingArrivalDateTime = \Carbon\Carbon::parse($existingCovoiturage->arrival_date . ' ' . $existingCovoiturage->arrival_time);

                if ($newDepartureDateTime < $existingArrivalDateTime && $newArrivalDateTime > $existingDepartureDateTime) {
                    $validator->errors()->add(
                        'overlap',
                        'Vous ne pouvez pas modifier ce covoiturage car il se chevauche avec un autre de vos trajets prévus entre le ' .
                            $existingDepartureDateTime->format('d/m/Y à H:i') . ' et le ' . $existingArrivalDateTime->format('d/m/Y à H:i') . '.'
                    );
                    break;
                }
            }
        });
    }
}
