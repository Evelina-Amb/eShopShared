<?php

namespace App\Repositories\Contracts;

interface CartRepositoryInterface
{
    public function getAll();
    public function getById(int $id);
    public function getByUser(int $userId);
    public function create(array $data);
    public function update($model, array $data);
    public function delete($model);
}
