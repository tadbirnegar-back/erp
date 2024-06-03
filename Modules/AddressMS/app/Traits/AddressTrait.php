<?php

namespace Modules\AddressMS\app\Traits;

use Modules\AddressMS\app\Models\Address;

trait AddressTrait
{
    public function addressStore(array $data)
    {
        $status = $this->activeAddressStatus();

        $address = new Address;
        $address->title = $data['title'];
        $address->detail = $data['address'];
        $address->postal_code = $data['postalCode'] ?? null;
        $address->longitude = $data['longitude'] ?? null;
        $address->latitude = $data['latitude'] ?? null;
        $address->map_link = $data['mapLink'] ?? null;
        $address->town_id = $data['townID'];
        $address->village_id = $data['villageID'] ?? null;
        $address->person_id = $data['personID'] ?? null;
        $address->status_id = $status->id;
        $address->creator_id = $data['userID'] ?? null;
        $address->save();
        return $address->load('village', 'town.district.city.state.country');


    }

    public function addressUpdate(array $data, Address $address)
    {
        $address->title = $data['title'];
        $address->detail = $data['address'];
        $address->postal_code = $data['postalCode'] ?? null;
        $address->longitude = $data['longitude'] ?? null;
        $address->latitude = $data['latitude'] ?? null;
        $address->map_link = $data['mapLink'] ?? null;
        $address->town_id = $data['townID'];
        $address->village_id = $data['villageID'] ?? null;
        $address->person_id = $data['personID'] ?? null;
        $address->creator_id = $data['userID'] ?? null;
        $address->save();
        return $address->load('village', 'town.district.city.state.country');


    }

    public function addressShow(int $id)
    {
        return Address::with('village','town.district.city.addresses.city.state.country','status')->findOrFail($id);
    }
    public function activeAddressStatus()
    {
        return Address::GetAllStatuses()->firstWhere('name', '=', 'فعال');
    }

    public function inactiveAddressStatus()
    {
        return Address::GetAllStatuses()->firstWhere('name', '=', 'غیرفعال');
    }

    public function allAddressStats()
    {
        return Address::GetAllStatuses();
    }
}
