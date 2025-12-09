<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryRepository  extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function getAll(): Collection
    {
        return Category::with('listing')->get();
    }

    public function getById(int $id): ?Category
    {
        return Category::with('listing')->find($id);
    }
} 
