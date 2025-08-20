<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePreferencesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pref_smoke' => ['required', 'string', Rule::in(['Fumeur', 'Non-fumeur'])],
            'pref_pet' => ['required', 'string', Rule::in(['Acceptés', 'Non-acceptés'])],
            'pref_libre' => ['nullable', 'string', 'max:255'],
        ];
    }
}