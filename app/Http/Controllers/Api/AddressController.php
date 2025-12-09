<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Http\Resources\BaseCollection;
use App\Services\AddressService;

class AddressController extends BaseController
{
    protected AddressService $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function index()
    {
        $addresses = $this->addressService->getAll();
        return $this->sendResponse(new BaseCollection($addresses, AddressResource::class), 'Addresses retrieved.');
    }

    public function show($id)
    {
        $address = $this->addressService->getById($id);
        if (!$address) return $this->sendError('Address not found.', 404);

        return $this->sendResponse(new AddressResource($address), 'Address found.');
    }

    public function store(StoreAddressRequest $request)
    {
        $address = $this->addressService->create($request->validated());
        return $this->sendResponse(new AddressResource($address), 'Address created.', 201);
    }

    public function update(UpdateAddressRequest $request, $id)
    {
        $address = $this->addressService->update($id, $request->validated());
        if (!$address) return $this->sendError('Address not found.', 404);

        return $this->sendResponse(new AddressResource($address), 'Address updated.');
    }

    public function destroy($id)
    {
        $deleted = $this->addressService->delete($id);
        if (!$deleted) return $this->sendError('Address not found.', 404);

        return $this->sendResponse(null, 'Address deleted.');
    }
}
