<?php

namespace Modules\CustomerMS\app\Http\Repositories;

use Modules\CustomerMS\app\Models\Customer;

class CustomerRepository
{
    protected Customer $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function store(array $data)
    {

    }


}
