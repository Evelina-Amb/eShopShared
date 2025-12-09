<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ivertinimas'=> $this->ivertinimas,
            'komentaras' => $this->komentaras,
            'data'       => $this->created_at?->format('Y-m-d H:i'),
            'vartotojas' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
