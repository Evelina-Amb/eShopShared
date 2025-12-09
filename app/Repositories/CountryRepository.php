<?php

namespace App\Repositories;

use App\Models\Country;
use App\Repositories\Contracts\CountryRepositoryInterface;
use Illuminate\Support\Collection;

class CountryRepository extends BaseRepository implements CountryRepositoryInterface
{
    public function __construct(Country $model)
    {
        parent::__construct($model);
    }

    public function getAll(): Collection
    {
        return Country::with('city')->get();
    }

    public function getById(int $id): ?Country
    {
        return Country::with('city')->find($id);
    }
}
