<?php

namespace App\Services;

use App\Models\Address;
use App\Repositories\Contracts\AddressRepositoryInterface;

class AddressService
{
    protected AddressRepositoryInterface $addressRepository;

    public function __construct(AddressRepositoryInterface $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    public function getAll()
    {
        return $this->addressRepository->getAll();
    }

    public function getById(int $id)
    {
        return $this->addressRepository->getById($id);
    }

    public function create(array $data)
    {
        return $this->addressRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $address = $this->addressRepository->getById($id);
        if (!$address) return null;

        return $this->addressRepository->update($address, $data);
    }

    public function delete(int $id)
    {
        $address = $this->addressRepository->getById($id);
        if (!$address) return false;

        return $this->addressRepository->delete($address);
    }
}
