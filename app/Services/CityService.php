<?php

namespace App\Services;

use App\Models\City;
use App\Repositories\Contracts\CityRepositoryInterface;

class CityService
{
    protected CityRepositoryInterface $cityRepository;

    public function __construct(CityRepositoryInterface $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    public function getAll()
    {
        return $this->cityRepository->getAll();
    }

    public function getById(int $id)
    {
        return $this->cityRepository->getById($id);
    }

    public function create(array $data)
    {
        return $this->cityRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $city = $this->cityRepository->getById($id);
        if (!$city) return null;

        return $this->cityRepository->update($city, $data);
    }

    public function delete(int $id)
    {
        $city = $this->cityRepository->getById($id);
        if (!$city) return false;

        return $this->cityRepository->delete($city);
    }
}
