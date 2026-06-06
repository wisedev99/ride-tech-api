<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'pickup_address'      => ['required', 'string', 'max:255'],
            'destination_address' => ['required', 'string', 'max:255'],
            'preferences'         => ['nullable', 'string'],
        ];
    }
}
