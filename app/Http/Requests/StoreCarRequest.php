<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        return [
            'brand'        => ['required', 'string', 'max:255'],
            'model'        => ['required', 'string', 'max:255'],
            'plate_number' => ['required', 'string', 'max:20', 'unique:cars,plate_number'],
        ];
    }
}
