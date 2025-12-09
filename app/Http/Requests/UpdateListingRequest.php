<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateListingRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'pavadinimas'   => 'sometimes|string|max:100',
            'aprasymas'     => 'sometimes|string',
            'kaina'         => 'sometimes|numeric|min:0',
            'tipas'         => 'sometimes|string|in:preke,paslauga',
            'category_id' => 'sometimes|exists:category,id',
            'statusas'      => 'sometimes|string|in:aktyvus,rezervuotas,parduotas'
        ];
    }
}
