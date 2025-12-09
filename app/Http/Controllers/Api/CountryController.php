<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Http\Resources\CountryResource;
use App\Http\Resources\BaseCollection;
use App\Services\CountryService;

class CountryController extends BaseController
{
    protected CountryService $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }
 
    public function index()
    {
        $countries = $this->countryService->getAll();
        return $this->sendResponse(new BaseCollection($countries, CountryResource::class), 'Countries retrieved.');
    }

    public function show($id)
    {
        $country = $this->countryService->getById($id);
        if (!$country) return $this->sendError('Country not found.', 404);

        return $this->sendResponse(new CountryResource($country), 'Country found.');
    }

    public function store(StoreCountryRequest $request)
    {
        $country = $this->countryService->create($request->validated());
        return $this->sendResponse(new CountryResource($country), 'Country created.', 201);
    }

    public function update(UpdateCountryRequest $request, $id)
    {
        $country = $this->countryService->update($id, $request->validated());
        if (!$country) return $this->sendError('Country not found.', 404);

        return $this->sendResponse(new CountryResource($country), 'Country updated.');
    }

    public function destroy($id)
    {
        $deleted = $this->countryService->delete($id);
        if (!$deleted) return $this->sendError('Country not found.', 404);

        return $this->sendResponse(null, 'Country deleted.');
    }
}
