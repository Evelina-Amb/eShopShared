<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'kaina'    => (float) $this->kaina,
            'skelbimas'=> new ListingResource($this->whenLoaded('Listing')),
        ];
    }
}
