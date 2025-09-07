<?php

namespace App\Http\Requests;

use App\Rules\Honeypot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreCovoiturageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Seuls les utilisateurs ayant un rôle "conducteurs" ou "les deux" peuvent créer un covoit
        return in_array(Auth::user()->role, ['Conducteur', 'Les deux']);
    }

    //Régle de validation
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
                // Le serveur doit s'assurer que la voiture sélectionnée appartient bien à l'utilisateur connecté => Protection IDOR (Insecure Direct Objec Reference)=> A NE PAS OUBLIER!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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
            'user_preferences' => ['nullable', new Honeypot],
        ];
    }

    // Empêcher le chevauchement des covoits
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = $this->user();

            $newDepartureDateTime = \Carbon\Carbon::parse($this->input('departure_date') . ' ' . $this->input('departure_time'));
            $newArrivalDateTime = \Carbon\Carbon::parse($this->input('arrival_date') . ' ' . $this->input('arrival_time'));

            $existingCovoiturages = \App\Models\Covoiturage::where('user_id', $user->user_id)
                ->where('cancelled', 0)
                ->get();

            foreach ($existingCovoiturages as $existingCovoiturage) {
                $existingDepartureDateTime = \Carbon\Carbon::parse($existingCovoiturage->departure_date . ' ' . $existingCovoiturage->departure_time);
                $existingArrivalDateTime = \Carbon\Carbon::parse($existingCovoiturage->arrival_date . ' ' . $existingCovoiturage->arrival_time);

                if ($newDepartureDateTime < $existingArrivalDateTime && $newArrivalDateTime > $existingDepartureDateTime) {
                    $validator->errors()->add(
                        'overlap',
                        'Vous ne pouvez pas créer ce covoiturage car il se chevauche avec un autre de vos trajets prévus entre le ' .
                            $existingDepartureDateTime->format('d/m/Y à H:i') . ' et le ' . $existingArrivalDateTime->format('d/m/Y à H:i') . '.'
                    );
                    break;
                }
            }
        });
    }
}
