<?php

namespace App\Repositories\Contracts;

use App\Models\Listing;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface ListingRepositoryInterface
{
    public function getAll();
    public function getById(int $id);
    public function create(array $data);
    public function update($model, array $data);
    public function delete($model);
}
