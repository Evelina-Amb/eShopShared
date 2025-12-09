<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'pavadinimas'  => 'required|string|max:100',
            'aprasymas'    => 'nullable|string|max:255',
            'tipo_zenklas' => 'required|string|in:preke,paslauga'
        ];
    }
}
