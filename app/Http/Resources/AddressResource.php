<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'gatve' => $this->gatve,
            'namo_nr' => $this->namo_nr,
            'buto_nr' => $this->buto_nr,
            'miestas' => new CityResource($this->whenLoaded('City')),
        ];
    }
}
