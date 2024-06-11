<?php

namespace Modules\CustomerMS\app\Http\Traits;

use Modules\CustomerMS\app\Models\Customer;
use Modules\CustomerMS\app\Models\ShoppingCustomer;
use Modules\PersonMS\app\Models\Legal;
use Modules\PersonMS\app\Models\Natural;

trait CustomerTrait
{
    public function customerIndex(array $data)
    {
        $page = $data['pageNumber'] ?? 1;
        $perPage = $data['perPage'] ?? 10;

        $customerQuery = Customer::with('person.avatar');

        //search by name
        if (isset($data['customerName'])) {
            $value = $data['customerName'];
            $customerQuery = $customerQuery->whereHas('person', function ($query) use ($value) {
                $query->whereRaw("MATCH(display_name) AGAINST(? IN BOOLEAN MODE)", [$value]);
            });
        }

        //filter by legal or natural
        if (isset($data['personableType'])) {
            $value = $data['personableType'] == 'legal' ? Legal::class : Natural::class;
            $customerQuery = $customerQuery->whereHas('person', function ($query) use ($value) {
                $query->where('personable_type', '=', $value);
            });
        }


        $paginator = $customerQuery->orderBy('create_date', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
//        $customerQuery->orderBy('create_date', 'desc')
//            ->paginate($perPage, ['*'], 'page', $page);

        $modifiedCollection = $paginator->getCollection()->each(function ($customer) {
            /**
             * @var Customer $customer
             */


            if ($customer->person && $customer->person->avatar) {
                $prefix = url('/') . '/';
                if (!str_starts_with($customer->person->avatar->slug, $prefix)) {
                    $link = $prefix . $customer->person->avatar->slug;
                    $customer->person->avatar->slug = $link;
                }
                $customer->person->personable_type = $customer->person->personable_type == Legal::class ? 'legal' : 'natural';

            }
        });

        $paginator->setCollection($modifiedCollection);

        return $paginator;
    }

    public function customerStore(array $data)
    {

            /**
             * @var ShoppingCustomer $shoppingCustomer
             */

            $shoppingCustomer = new ShoppingCustomer();
            $shoppingCustomer->save();
            /**
             * @var Customer $customer
             */
            $customer = new Customer();
            $customer->creator_id = $data['userID'];
            $customer->person_id = $data['personID'];
            $customer->customer_type_id = $data['customerTypeID'] ?? null;

            $status = $this->activeCustomerStatus() ;

            $customer->status_id = $status->id;
//            $customer->save();
            $shoppingCustomer->customer()->save($customer);

            return $customer;



    }

    public function customerUpdate(array $data,Customer $customer)
    {
        $customer->status_id = $data['statusID'];
        $customer->save();
    }

    public function isPersonCustomer(int $personID)
    {
        $customer = Customer::where('person_id', '=', $personID)->first();
        return $customer == true;
    }

    public function customerShow(Customer $customer)
    {
        $customer->load('person.avatar', 'person.personable', 'status','customerable');

        if ($customer->person && $customer->person->personable instanceof Natural) {
            $customer->person->load(['personable.homeAddress.city.state.country']);
            return $customer;
        } elseif ($customer->person && $customer->person->personable instanceof Legal) {
            $customer->person->load(['personable.address.city.state.country']);
            return $customer;
        } else {
            return null;
        }
    }

    public function activeCustomerStatus()
    {
        return Customer::GetAllStatuses()->firstWhere('name', '=', 'فعال');
    }

    public function inActiveCustomerStatus()
    {
        return Customer::GetAllStatuses()->firstWhere('name', '=', 'غیرفعال');
    }

    public function allCustomerStats()
    {
        return Customer::GetAllStatuses();
    }

    public function customerDestroy(Customer $customer)
    {
        $status = $this->inActiveCustomerStatus();

        $customer->status_id = $status->id;
        $customer->save();

        return $customer;
    }
}
