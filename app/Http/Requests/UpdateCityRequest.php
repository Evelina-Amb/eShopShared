<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCityRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'pavadinimas' => 'sometimes|string|max:100',
            'country_id'    => 'sometimes|exists:country,id'
        ];
    }
}
