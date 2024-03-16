<?php

namespace Modules\PersonMS\app\Http\Repositories;

use DB;
use Modules\PersonMS\app\Models\Legal;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;

class PersonRepository
{
//    protected $person;
//    protected $natural;
//    protected $legal;
//
//    public function __construct(Natural $natural, Legal $legal, Person $person)
//    {
//        $this->person = $person;
//        $this->natural = $natural;
//        $this->legal = $legal;
//    }

    public function naturalExists(string $nationalCode): Person|null
    {
        $person = Person::with('personable.homeAddress.city.state.country', 'avatar', 'status')->where('national_code', '=', $nationalCode)->first();
//        if ($person && $person->personable instanceof Natural) {
//            // If personable is an instance of Natural, load the address relationship specifically for this instance
//            $person->load(['personable.homeAddress.city.state.country']); // Ensure that the relationship path is correct
//        }


        return $person;
    }

    public function naturalStore(array $data): Natural|\Exception
    {
        try {
            DB::beginTransaction();
//            if ($data['isNewAddress) {
//                $address = new Address();
//                $address->title = $data['title;
//                $address->detail = $data['address;
//                $address->postal_code = $data['postalCode ?? null;
//                $address->longitude = $data['longitude ?? null;
//                $address->latitude = $data['latitude ?? null;
//                $address->map_link = $data['mapLink ?? null;
//                $address->city_id = $data['cityID;
//                $address->status_id = Address::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
//                $address->creator_id = \Auth::user()->id;
//                $address->save();
//                $address = new AddressMSController;
//
//                $ai = $address->store($request);
//                return response()->json([$ai,
//                ]);
//                $addressID = $address->id;
//            } else {
//                $addressID = $data['homeAddressID;
//            }

            $naturalPerson = new Natural();
            $naturalPerson->first_name = $data['firstName'];
            $naturalPerson->last_name = $data['lastName'];
            $naturalPerson->mobile = $data['mobile'] ?? null;
            $naturalPerson->phone_number = $data['phoneNumber'] ?? null;
            $naturalPerson->father_name = $data['fatherName'] ?? null;
            $naturalPerson->birth_date = $data['dateOfBirth'] ?? null;
            $naturalPerson->job = $data['job'] ?? null;
            $naturalPerson->isMarried = $data['isMarried'] ? 1 : 0;
            $naturalPerson->level_of_spouse_education = $data['levelOfSpouseEducation'] ?? null;
            $naturalPerson->spouse_first_name = $data['spouseFirstName'] ?? null;
            $naturalPerson->spouse_last_name = $data['spouseLastName'] ?? null;
            $naturalPerson->home_address_id = $data['homeAddressID'] ?? null;
            $naturalPerson->job_address_id = $data['jobAddressID'] ?? null;
            $naturalPerson->gender_id = $data['gender'];
            $naturalPerson->military_service_status_id = $data['militaryServiceStatusID'] ?? null;


            $naturalPerson->save();

            $person = new Person();
            $person->display_name = $naturalPerson->first_name . ' ' . $naturalPerson->last_name;
            $person->national_code = $data['nationalCode'];
            $person->profile_picture_id = $data['avatar'] ?? null;
            $person->email = $data['email'] ?? null;
            $person->phone = $data['phone'] ?? null;

            $naturalPerson->person()->save($person);
            $status = Person::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
            $naturalPerson->person->status()->attach($status);


            DB::commit();
            return $naturalPerson;

        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
//            return response()->json('خطا در وارد کردن فرد جدید', 500);

        }

    }

    public function legalExists(string $name)
    {

        $result = Legal::with('address.city.state.country', 'person.statuses', 'person.avatar')->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", $name)->get();
        return $result;


    }

    public function legalStore(array $data)
    {
        try {
            DB::beginTransaction();

//            if ($request->isNewAddress) {
//                $address = new Address();
//                $address->title = $request->title;
//                $address->detail = $request->address;
//                $address->postal_code = $request->postalCode ?? null;
//                $address->longitude = $request->longitude ?? null;
//                $address->latitude = $request->latitude ?? null;
//                $address->map_link = $request->mapLink ?? null;
//                $address->city_id = $request->cityID;
//                $address->status_id = Address::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
//                $address->creator_id = \Auth::user()->id;
//                $address->save();
//
//                $addressID = $address->id;
//            } else {
//                $addressID = $request->businessAddressID ?? null;
//            }

            $legal = new Legal();
            $legal->name = $data['name'];
            $legal->registration_number = $data['registrationNumber'] ?? null;
            $legal->foundation_date = $data['foundationDate'] ?? null;
            $legal->legal_type_id = $data['legalTypeID'] ?? null;
            $legal->address_id = $data['businessAddressID'] ?? null;
            $legal->save();

            $person = new Person();
            $person->display_name = $legal->name;
            $person->national_code = $data['national_code'] ?? null;
            $person->profile_picture_id = $data['avatar'] ?? null;
            $person->phone = $data['phone'] ?? null;

            $legal->person()->save($person);
            $status = Person::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
            $legal->person->status()->attach($status);

            DB::commit();
            return $legal;

        } catch (\Exception $e) {
            DB::rollBack();
//            return response()->json(['message' => 'خطا در ثبت رکورد جدید'], 500);
            return $e;

        }

    }

    public function naturalUpdate(array $data, $id)
    {
        $naturalPerson = Natural::findOrFail($id);

        if ($naturalPerson == null) {
            return null;
        }

        try {
            DB::beginTransaction();


            $naturalPerson->first_name = $data['firstName'];
            $naturalPerson->last_name = $data['lastName'];
            $naturalPerson->mobile = $data['mobile'];
            $naturalPerson->phone_number = $data['phoneNumber'] ?? null;
            $naturalPerson->father_name = $data['fatherName'] ?? null;
            $naturalPerson->birth_date = $data['dateOfBirth'] ?? null;
            $naturalPerson->job = $data['job'] ?? null;
            $naturalPerson->isMarried = $data['isMarried'] ? 1 : 0;
            $naturalPerson->level_of_spouse_education = $data['levelOfSpouseEducation'] ?? null;
            $naturalPerson->spouse_first_name = $data['spouseFirstName'] ?? null;
            $naturalPerson->spouse_last_name = $data['spouseLastName'] ?? null;
            $naturalPerson->home_address_id = $data['homeAddressID'] ?? null;
            $naturalPerson->job_address_id = $data['jobAddressID'] ?? null;
            $naturalPerson->gender_id = $data['gender'];
            $naturalPerson->military_service_status_id = $data['militaryServiceStatusID'] ?? null;

            $naturalPerson->save();
            $person = $naturalPerson->person;
            $person->display_name = $naturalPerson->first_name . ' ' . $naturalPerson->last_name;
            $person->national_code = $data['nationalCode'];
            $person->profile_picture_id = $data['avatar'] ?? null;

            $naturalPerson->person()->save($person);
            $statusID = $person->status;
            if (isset($data['statusID']) && $statusID[0]->id != $data['statusID']) {
                $naturalPerson->person->status()->attach($data['statusID']);
            }
            DB::commit();
            return $naturalPerson;

        } catch (\Exception $e) {
            DB::rollBack();
            return $e;

        }
    }

    public function legalUpdate(array $data, $id)
    {
        $legal = Legal::findOrFail($id);

        if ($legal == null) {
            return null;
        }

        try {
            DB::beginTransaction();

            $legal->name = $data['name'];
            $legal->registration_number = $data['registrationNumber'] ?? null;
            $legal->foundation_date = $data['foundationDate'] ?? null;
            $legal->legal_type_id = $data['legalTypeID'] ?? null;
            $legal->address_id = $data['businessAddressID'] ?? null;
            $legal->save();

            $person = $legal->person;
            $person->display_name = $legal->name;
            $person->national_code = $data['national_code'] ?? null;
            $person->profile_picture_id = $data['avatar'] ?? null;
            $person->phone = $data['phone'] ?? null;

            $legal->person()->save($person);
            $status = Person::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
            $legal->person->status()->attach($status);

            DB::commit();
            return $legal;

        } catch (\Exception $e) {
            DB::rollBack();
            return $e;

        }

    }


}
