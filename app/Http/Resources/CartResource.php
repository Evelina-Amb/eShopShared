<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'kiekis'   => $this->kiekis,
            'preke'    => new ListingResource($this->whenLoaded('listing')),
            'vartotojas' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
