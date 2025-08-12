<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RechargeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Condition pour recharger son crédit */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'in:10,20,50,100,200'],
        ];
    }
}