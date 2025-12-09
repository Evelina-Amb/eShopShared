<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderItemRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'kaina'  => 'sometimes|numeric|min:0',
            'kiekis' => 'sometimes|integer|min:1'
        ];
    }
}
