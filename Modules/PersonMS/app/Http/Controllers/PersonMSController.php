<?php

namespace Modules\PersonMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\AddressMS\app\Http\Controllers\AddressMSController;
use Modules\AddressMS\app\Models\Address;
use Modules\AddressMS\app\services\AddressService;
use Modules\CustomerMS\app\Http\Services\CustomerService;
use Modules\PersonMS\app\Http\Services\PersonService;
use Modules\PersonMS\app\Models\Legal;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;

class PersonMSController extends Controller
{

    protected $customerService;
    protected $addressService;
    protected $personService;


    public function __construct(CustomerService $customerService, PersonService $personService, AddressService $addressService)
    {
        $this->customerService = $customerService;
        $this->addressService = $addressService;
        $this->personService = $personService;
    }


    public function naturalExists(Request $request)
    {
        $result = $this->personService->naturalExists($request->nationalCode);

        if ($result == null) {
            return response()->json(['message' => 'موردی یافت نشد']);
        }

        return $result;
    }

    /**
     * Display a listing of the resource.
     */
    public function naturalIndex(Request $request): JsonResponse
    {
        $page = $request->input('pageNumber', 1);
        $perPage = $request->input('perPage', 10);

        $naturalQuery = Natural::with('person.avatar');

        $searchTerm = $request->input('naturalName', ''); // Or however you get your search term

        $naturalQuery->when($searchTerm, function ($query) use ($searchTerm) {
            $query->whereRaw("MATCH (first_name,last_name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
        });
        $naturals = $naturalQuery->paginate($perPage, page: $page);

        return response()->json($naturals);
    }

    public function legalIndex(Request $request): JsonResponse
    {
        $page = $request->input('pageNumber', 1);
        $perPage = $request->input('perPage', 10);

        $naturalQuery = Legal::with('person.avatar');

        $searchTerm = $request->input('legalName', ''); // Or however you get your search term

        $naturalQuery->when($searchTerm, function ($query) use ($searchTerm) {
            $query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
        });
        $naturals = $naturalQuery->paginate($perPage, page: $page);

        return response()->json($naturals);
    }

    public function naturalStore(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [

            'nationalCode' => [
                'sometimes',
                'unique:persons,national_code',
            ],
            'mobile' => [
                'sometimes',
                'unique:naturals,mobile',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data['userID'] = \Auth::user()->id;

        if ($request->isNewAddress) {
            $address = $this->addressService->store($data);

            if ($address instanceof \Exception) {
                return response()->json(['message' => 'خطا در بروزرسانی مشتری'], 500);
            }

        }
        if (isset($address)) {
            $data['homeAddressID'] = $address->id;
        }
        $personResult = $this->personService->naturalStore($data);

        if ($personResult instanceof \Exception) {
            return response()->json(['message' => 'خطا در بروزرسانی مشتری'], 500);
        }
        return response()->json($personResult);

    }

    public function naturalShow($id)
    {
        $natural = Natural::with('person.avatar', 'person.status', 'homeAddress.city.state.country')->findOrFail($id);

        if ($natural == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }
        if (\Str::contains(\request()->route()->uri(), 'person/natural/edit/{id}')) {
            $statuses = Person::GetAllStatuses();

            $result['statuses'] = $statuses;
        }
        $result['natural'] = $natural;

        return response()->json($result);
    }

    public function naturalUpdate(Request $request, $id)
    {
        $naturalPerson = Natural::findOrFail($id);

        if ($naturalPerson == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        try {
            DB::beginTransaction();
            if ($request->isNewAddress) {
                $address = new Address();
                $address->title = $request->title;
                $address->detail = $request->address;
                $address->postal_code = $request->postalCode ?? null;
                $address->longitude = $request->longitude ?? null;
                $address->latitude = $request->latitude ?? null;
                $address->map_link = $request->mapLink ?? null;
                $address->city_id = $request->cityID;
                $address->status_id = Address::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
                $address->creator_id = \Auth::user()->id;
                $address->save();
                $addressID = $address->id;
            } else {
                $addressID = $request->homeAddressID;
            }

            $naturalPerson->first_name = $request->firstName;
            $naturalPerson->last_name = $request->lastName;
            $naturalPerson->mobile = $request->mobile;
            $naturalPerson->phone_number = $request->phoneNumber ?? null;
            $naturalPerson->father_name = $request->fatherName ?? null;
            $naturalPerson->birth_date = $request->dateOfBirth ?? null;
            $naturalPerson->job = $request->job ?? null;
            $naturalPerson->isMarried = $request->isMarried ?? null;
            $naturalPerson->level_of_spouse_education = $request->levelOfSpouseEducation ?? null;
            $naturalPerson->spouse_first_name = $request->spouseFirstName ?? null;
            $naturalPerson->spouse_last_name = $request->spouseLastName ?? null;
            $naturalPerson->home_address_id = $addressID ?? null;
            $naturalPerson->job_address_id = $request->jobAddressID ?? null;
            $naturalPerson->gender_id = $request->gender;
            $naturalPerson->military_service_status_id = $request->militaryServiceStatusID ?? null;

            $naturalPerson->save();
            $person = $naturalPerson->person;
            $person->display_name = $naturalPerson->first_name . ' ' . $naturalPerson->last_name;
            $person->national_code = $request->nationalCode;
            $person->profile_picture_id = $request->avatar ?? null;

            $naturalPerson->person()->save($person);
            $statusID = $person->status;
            if ($statusID[0]->id != $request->statusID) {
                $naturalPerson->person->status()->attach($request->statusID);
            }
            DB::commit();
            return response()->json($naturalPerson);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json('خطا در وارد کردن فرد جدید', 500);

        }
    }

    public function naturalDestroy($id)
    {
        $naturalPerson = Natural::findOrFail($id);

        if ($naturalPerson == null || $naturalPerson->person->status[0]->name === 'غیرفعال') {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }


        $status = Person::GetAllStatuses()->where('name', '=', 'غیرفعال')->first()->id;
        $naturalPerson->person->status()->attach($status);

        return response()->json(['message' => 'با موفقیت حذف شد']);
    }

    public function legalStore(Request $request)
    {
        try {
            DB::beginTransaction();

            if ($request->isNewAddress) {
                $address = new Address();
                $address->title = $request->title;
                $address->detail = $request->address;
                $address->postal_code = $request->postalCode ?? null;
                $address->longitude = $request->longitude ?? null;
                $address->latitude = $request->latitude ?? null;
                $address->map_link = $request->mapLink ?? null;
                $address->city_id = $request->cityID;
                $address->status_id = Address::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
                $address->creator_id = \Auth::user()->id;
                $address->save();

                $addressID = $address->id;
            } else {
                $addressID = $request->businessAddressID ?? null;
            }

            $legal = new Legal();
            $legal->name = $request->name;
            $legal->registration_number = $request->registrationNumber ?? null;
            $legal->foundation_date = $request->foundationDate ?? null;
            $legal->legal_type_id = $request->legalTypeID ?? null;
            $legal->address_id = $addressID;

            $legal->save();

            $person = new Person();
            $person->display_name = $legal->name;
            $person->national_code = $request->national_code;
            $person->profile_picture_id = $request->avatar;
            $person->phone = $request->phone;

            $legal->person()->save($person);
            $status = Person::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
            $legal->person->status()->attach($status);

            DB::commit();
            return response()->json(['message' => 'کسب و کار جدید با موفقیت ایجاد شد']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ثبت رکورد جدید'], 500);
//            return response()->json(['message' => $e->getMessage()], 500);

        }


    }

    public function legalShow($id)
    {
        $legal = Legal::with('person.avatar', 'person.status', 'address.city.state.country')->findOrFail($id);

        if ($legal == null || $legal->person->status[0]->name === 'غیرفعال') {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }
        if (\Str::contains(\request()->route()->uri(), 'person/legal/edit/{id}')) {
            $statuses = Person::GetAllStatuses();

            $result['statuses'] = $statuses;
        }
        $result['legal'] = $legal;

        return response()->json($result);
    }

    public function legalUpdate(Request $request, $id)
    {
        $legal = Legal::findOrFail($id);
        if ($legal == null || $legal->person->status[0]->name === 'غیرفعال') {
            return response()->json(['message' => 'کسب و کاری با این مشخصات یافت نشد'], 404);
        }
        try {
            DB::beginTransaction();

            if ($request->isNewAddress) {
                $address = new Address();
                $address->title = $request->title;
                $address->detail = $request->address;
                $address->postal_code = $request->postalCode ?? null;
                $address->longitude = $request->longitude ?? null;
                $address->latitude = $request->latitude ?? null;
                $address->map_link = $request->mapLink ?? null;
                $address->city_id = $request->cityID;
                $address->status_id = Address::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
                $address->creator_id = \Auth::user()->id;
                $address->save();

                $addressID = $address->id;
            } else {
                $addressID = $request->businessAddressID ?? null;
            }

            $legal->name = $request->name;
            $legal->registration_number = $request->registrationNumber ?? null;
            $legal->foundation_date = $request->foundationDate ?? null;
            $legal->legal_type_id = $request->legalTypeID ?? null;
            $legal->address_id = $addressID;

            $legal->save();

            $person = $legal->person;
            $person->display_name = $legal->name;
            $person->national_code = $request->national_code;
            $person->profile_picture_id = $request->avatar;
            $person->phone = $request->phone;
            $statusID = $person->status;
            if ($statusID[0]->id != $request->statusID) {
                $legal->person->status()->attach($request->statusID);
            }
            $legal->person()->save($person);


            DB::commit();
            return response()->json(['message' => 'کسب و کار با موفقیت ویرایش شد']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ثبت رکورد جدید'], 500);
//            return response()->json(['message' => $e->getMessage()], 500);

        }

    }

    public function legalDestroy($id)
    {
        $legal = Legal::findOrFail($id);
        if ($legal == null || $legal->person->status[0]->name === 'غیرفعال') {
            return response()->json(['message' => 'کسب و کاری با این مشخصات یافت نشد'], 404);
        }


        $status = Person::GetAllStatuses()->where('name', '=', 'غیرفعال')->first()->id;
        $legal->person->status()->attach($status);

        return response()->json(['message' => 'با موفقیت حذف شد']);
    }
}
