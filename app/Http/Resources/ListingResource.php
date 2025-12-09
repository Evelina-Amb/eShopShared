<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     *
     */
    public function toArray($request)
{
    return [
        'id' => $this->id,

        'pavadinimas' => $this->pavadinimas,
        'aprasymas' => $this->aprasymas,
        'kaina' => $this->kaina,
        'tipas' => $this->tipas,
        'statusas' => $this->statusas,

         'listing_photo' => $this->ListingPhoto->map(function ($photo) {
                return [
                    'id'        => $photo->id,
                    'failo_url' => $photo->failo_url,
                ];
            }),

        'pardavejas' => [
            'id' => $this->user->id ?? null,
            'vardas' => $this->user->name ?? null,
            'pavarde' => $this->user->surname ?? null,
            'el_pastas' => $this->user->email ?? null,
            'telefonas' => $this->user->phone ?? null,
            'role' => $this->user->role ?? null,
            'sukurta' => $this->user->created_at ?? null,
        ],

        'sukurta' => $this->created_at,
    ];
}

}
