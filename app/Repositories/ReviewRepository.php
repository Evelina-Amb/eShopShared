<?php

namespace App\Repositories;

use App\Models\Review;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use Illuminate\Support\Collection;

class ReviewRepository  extends BaseRepository implements ReviewRepositoryInterface
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }

    public function getAll(): Collection
    {
        return Review::with(['listing', 'user'])->get();
    }

    public function getById(int $id): ?Review
    {
        return Review::with(['listing', 'user'])->find($id);
    }
} 
