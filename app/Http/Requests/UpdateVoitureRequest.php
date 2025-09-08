<?php

namespace App\Http\Requests;

use App\Rules\Honeypot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVoitureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand' => ['required', 'string', 'max:12'],
            'model' => ['required', 'string', 'max:24'],
            'immat' => ['required', 'string', 'regex:/^(?:[A-Z]{2}-[0-9]{3}-[A-Z]{2}|[A-Z]{2}[0-9]{3}[A-Z]{2})$/'],
            'date_first_immat' => ['required', 'date', 'before_or_equal:today'],
            'color' => ['required', 'string', 'max:12'],
            'n_place' => ['required', 'integer', 'min:2', 'max:9'],
            'energie' => ['required', 'string', Rule::in(['Electrique', 'Hybride', 'Diesel/Gazole', 'Essence', 'GPL'])],
            'edit_vehicle_details' => ['nullable', new Honeypot],
        ];
    }
}
