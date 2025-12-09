<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
  
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'vardas'     => $this->vardas,
            'pavarde'    => $this->pavarde,
            'el_pastas'  => $this->el_pastas,
            'telefonas'  => $this->telefonas ?? 'Nenurodytas',
            'role'       => $this->role,
            'address'    => new AddressResource($this->whenLoaded('Address')),
            'sukurta'    => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}
