<?php

namespace Modules\AddressMS\app\Repositories;

use Modules\AddressMS\app\Models\Address;

class AddressRepository
{
    protected $address;

    public function __construct(Address $address)
    {
        $this->address = $address;
    }


    public function store(array $data)
    {
        try {
//            $address = new $this->address;
            $address = new Address();
            $address->title = $data['title'];
            $address->detail = $data['address'];
            $address->postal_code = $data['postalCode'] ?? null;
            $address->longitude = $data['longitude'] ?? null;
            $address->latitude = $data['latitude'] ?? null;
            $address->map_link = $data['mapLink'] ?? null;
            $address->city_id = $data['cityID'];
            $address->status_id = $this->address::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
            $address->creator_id = \Auth::user()->id;
            $address->save();
            return $address->load('city', 'state', 'country');

        } catch (\Exception $e) {
            return $e->getMessage();
//            return response()->json([$data->all(),'nigga'=>'nigga test']);
        }

    }
}
