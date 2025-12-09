<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'gatve'      => 'sometimes|string|max:100',
            'namo_nr'    => 'nullable|string|max:10',
            'buto_nr'    => 'nullable|string|max:10',
            'city_id' => 'sometimes|exists:city,id'
        ];
    }
}
