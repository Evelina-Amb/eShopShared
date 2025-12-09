<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryService
{
    protected CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAll()
    {
        return $this->categoryRepository->getAll();
    }

    public function getById(int $id)
    {
        return $this->categoryRepository->getById($id);
    }

    public function create(array $data)
    {
        return $this->categoryRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $category = $this->categoryRepository->getById($id);
        if (!$category) return null;

        return $this->categoryRepository->update($category, $data);
    }

    public function delete(int $id)
    {
        $category = $this->categoryRepository->getById($id);
        if (!$category) return false;

        return $this->categoryRepository->delete($category);
    }
}
