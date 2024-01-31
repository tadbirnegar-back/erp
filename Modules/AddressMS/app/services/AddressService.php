<?php

namespace Modules\AddressMS\App\services;

use Modules\AddressMS\app\Repositories\AddressRepository;

class AddressService
{
    protected $addressRepository;

    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    public function store(array $data)
    {
        return $this->addressRepository->store($data);
    }

}
