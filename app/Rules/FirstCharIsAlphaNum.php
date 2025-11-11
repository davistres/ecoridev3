<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

// Faire des DocBlocks dans les fichiers MODELS qui ont déjà été fait!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

class FirstCharIsAlphaNum implements Rule
{
    /**
     * Détermine si la validation est ok.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Si la valeur est nulle ou vide, la règle passe car elle sera gérée par 'nullable' ou 'required'.
        if (empty($value)) {
            return true;
        }

        // Vérifie si le premier caractère est alphanumérique.
        return ctype_alnum($value[0]);
    }

    /**
     * Message d'erreur.
     *
     * @return string
     */
    public function message()
    {
        return 'Le premier caractère doit être une lettre ou un chiffre.';
    }
}