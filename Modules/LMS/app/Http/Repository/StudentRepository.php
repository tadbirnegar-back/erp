<?php

namespace Modules\LMS\app\Http\Repository;

use Modules\CustomerMS\app\Models\Customer;
use Modules\LMS\app\Models\Student;

class StudentRepository
{

    public function isPersonStudent(int $personID)
    {
        $studentCustomer = Customer::where('person_id', '=', $personID)->where('customerable_type', '=', Student::class)->first();

        return $studentCustomer;
    }
    public function store(array $data)
    {
        try {
//            \DB::beginTransaction();
            /**
             * @var Student $studentCustomer
             */

            $studentCustomer = new Student();
            $studentCustomer->save();
            /**
             * @var Customer $customer
             */
            $customer = new Customer();
            $customer->creator_id = $data['userID'];
            $customer->person_id = $data['personID'];
            $customer->customer_type_id = $data['customerTypeID'] ?? null;

            $status = Customer::GetAllStatuses()->where('name', '=', 'فعال')->first();

            $customer->status_id = $status->id;
//            $customer->save();
            $studentCustomer->customer()->save($customer);
//            \DB::commit();

            return $customer;

        } catch (\Exception $e) {
            \DB::rollBack();
            return $e;
        }

    }

    public function index(int $perPage=10,int $pageNum=1)
    {
        $students = Student::with('customer.person.personable.avatar');
    }

}
