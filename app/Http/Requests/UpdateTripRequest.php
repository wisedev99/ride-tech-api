<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pickup_address'      => ['sometimes', 'required', 'string', 'max:255'],
            'destination_address' => ['sometimes', 'required', 'string', 'max:255'],
            'preferences'         => ['nullable', 'string'],
        ];
    }
}
