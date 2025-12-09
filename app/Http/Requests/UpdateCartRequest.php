<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'kiekis' => 'sometimes|integer|min:1'
        ];
    }
}
