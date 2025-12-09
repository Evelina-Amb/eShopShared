<?php

namespace App\Repositories\Contracts;

interface ReviewRepositoryInterface
{
    public function getAll();
    public function getById(int $id);
    public function create(array $data);
    public function update($model, array $data);
    public function delete($model);
}
