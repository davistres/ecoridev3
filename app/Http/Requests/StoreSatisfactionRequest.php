<?php

namespace App\Http\Requests;

use App\Rules\Honeypot;
use App\Rules\FirstCharIsAlphaNum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

// Faire des DocBlocks dans les fichiers MODELS qui ont déjà été fait!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

class StoreSatisfactionRequest extends FormRequest
{
    /**
     * L'utilisateur est-il autorisé à faire cette demande ?
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour la demande de stockage de satisfaction.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'satisfaction_id' => 'required|exists:satisfaction,satisfaction_id',
            'covoit_id' => 'required|exists:covoiturage,covoit_id',
            'feeling' => 'required|boolean',
            'comment' => ['nullable', 'string', 'max:5000', new FirstCharIsAlphaNum],
            'review' => ['nullable', 'string', 'max:1200', new FirstCharIsAlphaNum],
            'note' => 'nullable|integer|min:1|max:5',
            'user_nickname' => ['nullable', new Honeypot], // Champ Honeypot
        ];
    }

    /**
     * Configure validator après les règles de base.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            // Si l'utilisateur est insatisfait (feeling = 0), le commentaire devient obligatoire.
            if ($this->input('feeling') == 0 && empty($this->input('comment'))) {
                $validator->errors()->add('comment', 'Le commentaire est obligatoire si vous n\'êtes pas satisfait.');
            }

            // Si un avis est laissé, la note devient obligatoire.
            if (!empty($this->input('review')) && empty($this->input('note'))) {
                $validator->errors()->add('note', 'La note est obligatoire si vous laissez un avis.');
            }
        });
    }
}