<?php

namespace App\Http\Requests;

use App\Rules\Honeypot;
use Illuminate\Foundation\Http\FormRequest;

class ProfilePhotoRequest extends FormRequest
{
    /** ME SOUVENIR : En fait c'est OBLIGATOIRE de faire une méthode authorize() pour une classe de Form Request dans Laravel
     * Même si il n'y a pas de logique complexe, même si ça renvoi toujours true, c'est OBLIGATOIRE!!! */
    public function authorize(): bool
    {
        return true;
    }

    /** Condition pour la validation des photos de profil */
    public function rules(): array
    {
        return [
            'profile_photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], // 2MB Max
            'photo_description' => ['nullable', new Honeypot],
        ];
    }
}