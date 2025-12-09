<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'pavadinimas'  => $this->pavadinimas,
            'aprasymas'    => $this->aprasymas ?? null,
            'tipo_zenklas' => $this->tipo_zenklas,
        ];
    }
}
