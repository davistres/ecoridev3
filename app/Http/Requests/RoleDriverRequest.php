<?php

namespace App\Http\Requests;

use App\Rules\Honeypot;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class RoleDriverRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    /** Condition pour valider le changement de rôle = passager à conducteur ou les deux */
    public function rules(): array
    {
        return [
            // Préférence conducteur
            'new_role' => ['required', 'string', Rule::in(['Conducteur', 'Les deux'])],
            'pref_smoke' => ['required', Rule::in(['Fumeur', 'Non-fumeur'])],
            'pref_pet' => ['required', Rule::in(['Acceptés', 'Non-acceptés'])],
            'pref_libre' => ['nullable', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'n_secu_social' => ['nullable', new Honeypot],

            // Informations véhicule
            'brand' => ['required', 'string', 'max:12'],
            'model' => ['required', 'string', 'max:24'],
            'immat' => ['required', 'string', 'max:10', Rule::unique('voiture', 'immat')],
            'date_first_immat' => ['required', 'date', 'before_or_equal:today'],
            'color' => ['required', 'string', 'max:12'],
            'n_place' => ['required', 'integer', 'min:2', 'max:9'],
            'energie' => ['required', 'string', Rule::in(['Electrique', 'Hybride', 'Diesel/Gazole', 'Essence', 'GPL'])],
        ];
    }
}