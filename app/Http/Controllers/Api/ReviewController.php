<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ReviewResource;
use App\Services\ReviewService;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\BaseCollection;

class ReviewController extends BaseController
{
    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function index()
    {
        $reviews = $this->reviewService->getAll();
        return $this->sendResponse(new BaseCollection($reviews, ReviewResource::class),'Reviews retrieved.');
    }

    public function show($id)
    {
        $review = $this->reviewService->getById($id);
        if (!$review) return $this->sendError('Review not found.', 404);

        return $this->sendResponse(new ReviewResource($review), 'Review found.');
    }

    public function store(StoreReviewRequest $request)
    {
        try {
            $review = $this->reviewService->create($request->validated());
            return $this->sendResponse(new ReviewResource($review), 'Review created.', 201);
        }
        catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 400);
        }
    }

    public function update(UpdateReviewRequest $request, $id)
    {
        $review = $this->reviewService->update($id, $request->validated());
        if (!$review) return $this->sendError('Review not found.', 404);

        return $this->sendResponse(new ReviewResource($review), 'Review updated.');
    }

    public function destroy($id)
    {
        $deleted = $this->reviewService->delete($id);
        if (!$deleted) return $this->sendError('Review not found.', 404);

        return $this->sendResponse(null, 'Review deleted.');
    }
}
