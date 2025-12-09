<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'ivertinimas' => 'sometimes|integer|min:1|max:5',
            'komentaras'  => 'nullable|string'
        ];
    }
}
