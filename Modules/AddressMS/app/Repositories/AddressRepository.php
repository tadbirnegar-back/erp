<?php

namespace Modules\AddressMS\app\Repositories;

use Modules\AddressMS\app\Models\Address;

class AddressRepository
{
//    protected $address;
//
//    public function __construct(Address $address)
//    {
//        $this->address = $address;
//    }


    public function store(array $data)
    {
        try {
            \DB::beginTransaction();
            $address = new Address;
//            $address = new Address();
            $address->title = $data['title'];
            $address->detail = $data['address'];
            $address->postal_code = $data['postalCode'] ?? null;
            $address->longitude = $data['longitude'] ?? null;
            $address->latitude = $data['latitude'] ?? null;
            $address->map_link = $data['mapLink'] ?? null;
            $address->town_id = $data['townID'];
            $address->village_id = $data['villageID'] ?? null;
            $address->person_id = $data['personID'] ?? null;
            $address->status_id = Address::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
            $address->creator_id = $data['userID']??null;
            $address->save();
            \DB::commit();
            return $address->load('city', 'state', 'country');

        } catch (\Exception $e) {
            \DB::rollBack();
            return $e;
        }

    }
}
