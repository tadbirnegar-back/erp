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
}
