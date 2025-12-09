<?php

namespace App\Services;

use App\Models\Country;
use App\Repositories\Contracts\CountryRepositoryInterface;

class CountryService
{
    protected CountryRepositoryInterface $countryRepository;

    public function __construct(CountryRepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function getAll()
    {
        return $this->countryRepository->getAll();
    }

    public function getById(int $id)
    {
        return $this->countryRepository->getById($id);
    }

    public function create(array $data)
    {
        return $this->countryRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $country = $this->countryRepository->getById($id);
        if (!$country) return null;

        return $this->countryRepository->update($country, $data);
    }

    public function delete(int $id)
    {
        $country = $this->countryRepository->getById($id);
        if (!$country) return false;

        return $this->countryRepository->delete($country);
    }
}
