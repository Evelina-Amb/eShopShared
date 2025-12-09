<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'vartotojas' => new UserResource($this->whenLoaded('user')),
            'skelbimas' => new ListingResource($this->whenLoaded('Listing')),
        ];
    }
}
