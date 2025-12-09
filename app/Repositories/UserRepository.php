<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getAll(): Collection
    {
        return User::with(['address', 'listing', 'review', 'cart', 'favorite', 'order'])->get();
    }

    public function getById(int $id): ?User
    {
        return User::with(['address', 'listing', 'review', 'cart', 'favorite', 'order'])->find($id);
    }
}
