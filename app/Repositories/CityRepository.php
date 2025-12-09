<?php

namespace App\Repositories;

use App\Models\City;
use App\Repositories\Contracts\CityRepositoryInterface;
use Illuminate\Support\Collection;

class CityRepository extends BaseRepository implements CityRepositoryInterface
{

    public function __construct(City $model)
    {
        parent::__construct($model);
    }

    public function getAll(): Collection
    {
        return City::with(['country', 'address'])->get();
    }

    public function getById(int $id): ?City
    {
        return City::with(['country', 'address'])->find($id);
    }
} 
