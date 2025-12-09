<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\BaseCollection;
use App\Services\CityService;

class CityController extends BaseController
{
    protected CityService $cityService;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    public function index()
    {
        $cities = $this->cityService->getAll();
        return $this->sendResponse(new BaseCollection($cities, CityResource::class), 'Cities retrieved.');
    }

    public function show($id)
    {
        $city = $this->cityService->getById($id);
        if (!$city) return $this->sendError('City not found.', 404);

        return $this->sendResponse(new CityResource($city), 'City found.');
    }

    public function store(StoreCityRequest $request)
    {
        $city = $this->cityService->create($request->validated());
        return $this->sendResponse(new CityResource($city), 'City created.', 201);
    }

    public function update(UpdateCityRequest $request, $id)
    {
        $city = $this->cityService->update($id, $request->validated());
        if (!$city) return $this->sendError('City not found.', 404);

        return $this->sendResponse(new CityResource($city), 'City updated.');
    }

    public function destroy($id)
    {
        $deleted = $this->cityService->delete($id);
        if (!$deleted) return $this->sendError('City not found.', 404);

        return $this->sendResponse(null, 'City deleted.');
    }
}
