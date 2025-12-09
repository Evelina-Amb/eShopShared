<?php

namespace App\Repositories\Contracts;

interface ListingPhotoRepositoryInterface
{
    public function getAll();
    public function getById(int $id);
    public function create(array $data);
    public function update($model, array $data);
    public function delete($model);
}
