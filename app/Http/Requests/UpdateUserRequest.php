<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'vardas'      => 'sometimes|string|max:50',
            'pavarde'     => 'sometimes|string|max:50',
            'el_pastas'   => 'sometimes|email|unique:users,el_pastas,' . $this->route('id'),
            'slaptazodis' => 'sometimes|string|min:6',
            'telefonas'   => 'nullable|string|max:30',
            'address_id'  => 'sometimes|exists:address,id',
            'role'        => 'sometimes|string|in:admin,seller'
        ];
    }
}
