<?php

namespace Modules\CustomerMS\app\Http\Services;

use Modules\CustomerMS\app\Http\Repositories\CustomerRepository;

class CustomerService
{
    protected CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function index(array $data)
    {
        return $this->customerRepository->index($data);
    }

    public function store(array $data)
    {
        return $this->customerRepository->store($data);
    }

    public function isPersonCustomer(int $personID)
    {
        return $this->customerRepository->isPersonCustomer($personID);
    }

    public function show(int $id)
    {
        return $this->customerRepository->show($id);
    }
}
