<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
{
    /** Utilité??? Voir DemandeRechercheCovoit.php */
    public function authorize(): bool
    {
        return true;
    }

    /** Règles pour la validation */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:18',
            'email' => 'required|email|max:255',
            'sujet' => 'required|string|in:Support technique,Problème lié à une réservation,Autre',
            'message' => 'required|string',
        ];
    }
}
