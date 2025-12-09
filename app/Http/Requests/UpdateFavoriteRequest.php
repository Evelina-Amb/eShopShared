<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFavoriteRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'user_id'      => 'sometimes|exists:users,id',
            'listing_id' => 'sometimes|exists:listing,id'
        ];
    }
}
