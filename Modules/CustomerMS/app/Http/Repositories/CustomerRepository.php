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
        try {
            \DB::beginTransaction();
            /**
             * @var Customer $customer
             */
            $customer = new $this->customer();
            $customer->creator_id = $data['userID'];
            $customer->person_id = $data['personID'];
            $customer->customer_type_id = $data['customerTypeID'] ?? null;

            $status = $this->customer::GetAllStatuses()->where('name', '=', 'فعال')->first();

            $customer->status_id = $status->id;
            $customer->save();
            \DB::commit();

            return $customer;

        } catch (\Exception $e) {
            \DB::rollBack();
            return $e;
        }

    }

    public function isPersonCustomer(int $personID)
    {
        $customer = $this->customer::where('person_id', '=', $personID)->first();
        return $customer == true;
    }


}
