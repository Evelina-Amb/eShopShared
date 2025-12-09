<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'pavadinimas'  => 'sometimes|string|max:100',
            'aprasymas'    => 'nullable|string|max:255',
            'tipo_zenklas' => 'sometimes|string|in:preke,paslauga'
        ];
    }
}
