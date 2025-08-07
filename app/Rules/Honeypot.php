<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;


/** Pour avoir le modél: "php artisan make:rule Honeypot" */
class Honeypot implements Rule
{
    /** Règle de validation... Avec PASSES et MESSAGE... Si la validation échoue, on retourne un message d'erreur. */
    public function passes($attribute, $value)
    {
        return is_null($value);
    }

    public function message()
    {
        return 'La validation a échoué.';
    }
}