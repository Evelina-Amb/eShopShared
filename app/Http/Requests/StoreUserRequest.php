<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'vardas'      => 'required|string|max:50',
            'pavarde'     => 'required|string|max:50',
            'el_pastas'   => 'required|email|unique:users,el_pastas',
            'slaptazodis' => 'required|string|min:6',
            'telefonas'   => 'nullable|string|max:30',
            'address_id'  => 'required|exists:address,id',
            'role'        => 'required|string|in:admin,seller'
        ];
    }
}
