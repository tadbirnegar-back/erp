<?php

namespace Modules\CustomerMS\app\Http\Repositories;

use Modules\CustomerMS\app\Models\Customer;
use Modules\CustomerMS\app\Models\ShoppingCustomer;
use Modules\PersonMS\app\Models\Legal;
use Modules\PersonMS\app\Models\Natural;

class CustomerRepository
{
    protected ShoppingCustomer $shoppingCustomer;
    protected Customer $customer;

    public function __construct(ShoppingCustomer $shoppingCustomer, Customer $customer)
    {
        $this->shoppingCustomer = $shoppingCustomer;
        $this->customer = $customer;
    }

    public function index(array $data)
    {
        $page = $data['pageNumber'] ?? 1;
        $perPage = $data['perPage'] ?? 10;

        $customerQuery = $this->customer::with('person.avatar');

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

    public function store(array $data)
    {
        try {
            \DB::beginTransaction();
            /**
             * @var ShoppingCustomer $shoppingCustomer
             */

            $shoppingCustomer = new $this->shoppingCustomer();
            $shoppingCustomer->save();
            /**
             * @var Customer $customer
             */
            $customer = new $this->customer();
            $customer->creator_id = $data['userID'];
            $customer->person_id = $data['personID'];
            $customer->customer_type_id = $data['customerTypeID'] ?? null;

            $status = $this->customer::GetAllStatuses()->where('name', '=', 'فعال')->first();

            $customer->status_id = $status->id;
//            $customer->save();
            $shoppingCustomer->customer()->save($customer);
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

    public function show(int $id)
    {
        $customer = $this->customer::with('person.avatar', 'person.personable', 'status','customerable')->findOrFail($id);

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
}
