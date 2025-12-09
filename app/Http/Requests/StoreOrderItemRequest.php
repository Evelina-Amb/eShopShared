<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderItemRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'order_id'  => 'required|exists:order,id',
            'listing_id' => 'required|exists:listing,id',
            'kaina'        => 'required|numeric|min:0',
            'kiekis'       => 'required|integer|min:1'
        ];
    }
}
