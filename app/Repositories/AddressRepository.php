<?php

namespace App\Repositories;

use App\Models\Address;
use App\Repositories\Contracts\AddressRepositoryInterface;
use Illuminate\Support\Collection;

class AddressRepository extends BaseRepository implements AddressRepositoryInterface
{
    public function __construct(Address $model)
    {
        parent::__construct($model);
    }

    public function getAll(): Collection
    {
        return Address::with(['city', 'users'])->get();
    }

    public function getById(int $id): ?Address
    {
        return Address::with(['city', 'users'])->find($id);
    }

}
