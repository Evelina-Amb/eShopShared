<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'user_id'     => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.listing_id' => 'required|exists:listing,id',
            'items.*.kiekis'     => 'required|integer|min:1',
        ];
    }
}
