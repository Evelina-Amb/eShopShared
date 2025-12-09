<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\BaseCollection;
use App\Services\CategoryService;

class CategoryController extends BaseController
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->getAll();
        return $this->sendResponse(new BaseCollection($categories,  CategoryResource::class), 'Categories retrieved.');
    }

    public function show($id)
    {
        $category = $this->categoryService->getById($id);
        if (!$category) return $this->sendError('Category not found.', 404);

        return $this->sendResponse(new CategoryResource($category), 'Category found.');
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->create($request->validated());
        return $this->sendResponse(new CategoryResource($category), 'Category created.', 201);
    }


    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->categoryService->update($id, $request->validated());
        if (!$category) return $this->sendError('Category not found.', 404);

    return $this->sendResponse(new CategoryResource($category), 'Category updated.');
    }

    public function destroy($id)
    {
        $deleted = $this->categoryService->delete($id);
        if (!$deleted) return $this->sendError('Category not found.', 404);

        return $this->sendResponse(null, 'Category deleted.');
    }
}
