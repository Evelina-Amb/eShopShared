<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateListingPhotoRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'failo_url' => 'sometimes|string|max:255'
        ];
    }
}
