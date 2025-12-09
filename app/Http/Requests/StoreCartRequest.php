<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'user_id'      => 'required|exists:users,id',
            'listing_id' => 'required|exists:listing,id',
            'kiekis'       => 'required|integer|min:1'
        ];
    }
}
