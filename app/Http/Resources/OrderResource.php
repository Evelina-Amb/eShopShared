<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'bendra_suma' => (float) $this->bendra_suma,
            'statusas'    => $this->statusas,
            'pirkimo_data'=> $this->created_at?->format('Y-m-d H:i'),
            'vartotojas'  => new UserResource($this->whenLoaded('user')),
            'prekes'      => OrderItemResource::collection($this->whenLoaded('OrderItem')),
        ];
    }
}
