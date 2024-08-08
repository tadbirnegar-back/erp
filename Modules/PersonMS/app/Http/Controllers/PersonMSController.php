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
use Modules\AddressMS\app\Traits\AddressTrait;
use Modules\CustomerMS\app\Http\Services\CustomerService;
use Modules\HRMS\app\Http\Traits\CourseRecordTrait;
use Modules\HRMS\app\Http\Traits\EducationRecordTrait;
use Modules\HRMS\app\Http\Traits\IsarTrait;
use Modules\HRMS\app\Http\Traits\MilitaryServiceTrait;
use Modules\HRMS\app\Http\Traits\RelativeTrait;
use Modules\HRMS\app\Http\Traits\ResumeTrait;
use Modules\HRMS\app\Http\Traits\SkillTrait;
use Modules\HRMS\app\Http\Traits\SkillWorkForceTrait;
use Modules\HRMS\app\Models\CourseRecord;
use Modules\HRMS\app\Models\EducationalRecord;
use Modules\HRMS\app\Models\ExemptionType;
use Modules\HRMS\app\Models\Isar;
use Modules\HRMS\app\Models\MilitaryService;
use Modules\HRMS\app\Models\MilitaryServiceStatus;
use Modules\HRMS\app\Models\Relative;
use Modules\HRMS\app\Models\Resume;
use Modules\HRMS\app\Models\Skill;
use Modules\HRMS\app\Models\SkillWorkForce;
use Modules\PersonMS\app\Http\Services\PersonService;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Legal;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;
use Modules\PersonMS\app\Models\Religion;
use Modules\PersonMS\app\Models\ReligionType;

class PersonMSController extends Controller
{
    use PersonTrait, AddressTrait, SkillTrait, RelativeTrait, SkillWorkForceTrait, EducationRecordTrait, CourseRecordTrait, ResumeTrait, MilitaryServiceTrait, IsarTrait;

    public function naturalExists(Request $request)
    {
        $result = $this->naturalPersonExists($request->nationalCode);

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

    public function personShow($id)
    {
        $person = Person::with(['avatar',
            'personable.religion',
            'personable.religionType',
            'user.roles',
            'workForce.skills',
            'workForce.educationalRecords.levelOfEducation',
            'workForce.resumes',
            'workForce.militaryStatus',
            'workForce.relatives.relativeType',
            'workForce.relatives.levelOfEducation',
            'workForce.courseRecords',
            'workForce.isars.isarStatus',
            'workForce.isars.relativeType',
            'employee.recruitmentScripts',
            'workForce.militaryService.militaryServiceStatus',
            'workForce.militaryService.exemptionType'])
            ->findOr($id, function () {

                return response()->json(['message' => 'موردی یافت نشد'], 404);
            });

        return response()->json($person);
    }

    public function naturalPersonUpdate(Request $request, $id)
    {
        $person = Person::with('personable')->findOrFail($id);

        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }
        $data = $request->all();

        try {
            DB::beginTransaction();

            $data['userID'] = \Auth::user()->id;

            if ($request->isNewAddress) {
                $address = $this->addressStore($data);
                $addressID = $address->id;
            } else {
                $addressID = $request->homeAddressID;
            }
            $data['homeAddressID'] = $addressID;


            $personUpdate = $this->personNaturalUpdate($data, $person);
            DB::commit();
            return response()->json($person);

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

    public function religionIndex()
    {
        $data['religion'] = Religion::all();
        $data['religionType'] = ReligionType::all();

        return response()->json($data);
    }

    public function militaryStatusesIndex()
    {
        $result['militaryStatus'] = MilitaryServiceStatus::all();
        $result['exemptionTypes'] = ExemptionType::all();

        return response()->json($result);
    }

    public function personProfileUpdate(Request $request, $id)
    {
        $person = Person::with('user')->findOrFail($id);

        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        $validator = Validator::make($data, [
            'username' => [
                'sometimes',
                'unique:users,username,' . $person->user->id,
            ],
            'currentPassword' => [
                'sometimes',
                'required',
            ],
            'newPassword' => [
                'sometimes',
                'required',
            ],
            'roles' => [
                'required',
                'regex:/^\[\d+(,\d+)*\]$/',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $person->profile_picture_id = $data['avatar'] ?? null;
        $person->save();

        $user = $person->user;
        try {
            DB::beginTransaction();
            if (isset($data['isNewPassword']) && $data['isNewPassword'] === true) {
                if (\Hash::check($request->currentPassword, $user->password)) {
                    $user->password = \Hash::make($request->newPassword);
                    $message = 'با موفقیت بروزرسانی شد';
                    $statusCode = 200;
                } else {
                    $message = 'رمز فعلی نادرست است';
                    $statusCode = 401;

                }

            } else {
                $message = 'با موفقیت بروزرسانی شد';
                $statusCode = 200;
            }

            $user->username = $data['username'] ?? null;
            $user->save();

            $roles = json_decode($data['roles'], true);
            $user->roles()->sync($roles);

            DB::commit();
            return response()->json(['message' => $message], $statusCode);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش اطلاعات کاربری'], 500);
        }
    }

    public function personalUpdate($id, Request $request)
    {
        $person = Person::with('personable')->findOrFail($id);

        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'nationalCode' => [
                'required',
                'unique:persons,national_code,' . $person->id,
            ],
            'email' => [
                'sometimes',
                'unique:users,email,' . $person->user->id,
                'unique:persons,email,' . $person->id,
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            DB::beginTransaction();
            $this->personNaturalUpdate($data, $person);

            DB::commit();
            return response()->json(['message' => 'اطلاعات شخصی با موفقیت ویرایش شد']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش اطلاعات شخصی', 'error' => $e->getMessage()], 500);
        }
    }

    public function contactInfoUpdate($id, Request $request)
    {
        $person = Person::with('personable', 'user')->findOrFail($id);

        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();

            $validator = Validator::make($data, [
                'mobile' => [
                    'required',
                    'unique:users,mobile,' . $person->user->id,
                ],
                'email' => [
                    'sometimes',
                    'unique:users,email,' . $person->user->id,
                    'unique:persons,email,' . $person->id,
                ],
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data['userID'] = \Auth::user()->id;

//            if ($request->isNewAddress) {
//                $address = $this->addressStore($data);
//                $addressID = $address->id;
//            } else {
            $addressID = $request->homeAddressID ?? null;
//            }
            $data['homeAddressID'] = $addressID;

            $user = $person->user;
            /**
             * @var Natural $natural
             */
            $natural = $person->personable;

            $natural->mobile = $data['mobile'] ?? null;
            $natural->home_address_id = $data['homeAddressID'] ?? null;
            $natural->save();

            $person->phone = $data['phone'] ?? null;
            $person->email = $data['email'] ?? null;
            $person->save();

            $user->mobile = $data['mobile'] ?? null;
            $user->email = $data['email'] ?? null;
            $user->save();

            DB::commit();
            return response()->json(['message' => 'اطلاعات تماس با موفقیت ویرایش شد']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش اطلاعات تماس'], 500);
        }
    }

    public function updatePersonnelInfo($id, Request $request)
    {
        $person = Person::with('employee')->findOrFail($id);

        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();
            $employee = $person->employee;
            $employee->personnel_code = $data['personnelCode'] ?? null;
            $employee->save();

            DB::commit();
            return response()->json(['message' => 'اطلاعات پرسنلی با موفقیت ویرایش شد']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش اطلاعات پرسنلی'], 500);
        }
    }

    public function storeSkillPerson($id, Request $request)
    {
        $person = Person::with('workForce')->findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();
            $skill = $this->swSingleStore($data, $person->workForce);

            DB::commit();
            return response()->json(['message' => 'مهارت ها با موفقیت ویرایش شد', 'data' => $skill]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش مهارت ها'], 500);
        }
    }

    public function updateSkillPerson($id, Request $request)
    {
        $person = Person::findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();
            $skill = $this->swUpdate(data: $data, workForce: $person->workForce);

            DB::commit();
            return response()->json(['message' => 'مهارت ها با موفقیت ویرایش شد', 'data' => $skill]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش مهارت ها'], 500);
        }
    }

    public function destroySkillPerson($id, Request $request)
    {
        $person = Person::with('workForce')->findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();
            $skill = SkillWorkForce::find($data['swID'])?->delete();

            DB::commit();
            return response()->json(['message' => 'مهارت با موفقیت حذف شد']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در حذف مهارت'], 500);
        }
    }

    public function storeRelativePerson($id, Request $request)
    {
        $person = Person::with('workForce')->findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();
            $relative = $this->relativeStore($data, $person->workForce->id);


            DB::commit();
            return response()->json(['message' => 'بستگان با موفقیت ویرایش شد', 'data' => $relative]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش بستگان', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateRelativePerson($id, Request $request)
    {
        $person = Person::findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();
            $relative = Relative::find($data['relativeID']);
            $data['workForceID'] = $person->workForce->id;
            $relative = $this->relativeUpdate($data, $relative);

            DB::commit();
            return response()->json(['message' => 'بستگان با موفقیت ویرایش شد', 'data' => $relative]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش بستگان'], 500);
        }
    }

    public function destroyRelativePerson($id, Request $request)
    {
        $person = Person::findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();
            $relative = Relative::find($data['relativeID']);
            $relative->delete();

            DB::commit();
            return response()->json(['message' => 'بستگان با موفقیت حذف شد']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در حذف بستگان'], 500);
        }
    }

    public function storeEducationalRecordPerson($id, Request $request)
    {
        $person = Person::findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();
            $educationalRecord = $this->educationalRecordStore($data, $person->workForce->id);

            DB::commit();
            return response()->json(['message' => 'سوابق تحصیلی با موفقیت ویرایش شد', 'data' => $educationalRecord]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش سوابق تحصیلی'], 500);
        }
    }

    public function updateEducationalRecordPerson($id, Request $request)
    {
        $person = Person::findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();
            $educationalRecord = EducationalRecord::find($data['erID']);
            $data['workForceID'] = $person->workForce->id;
            $educationalRecord = $this->educationalRecordUpdate($data, $educationalRecord);

            DB::commit();
            return response()->json(['message' => 'سوابق تحصیلی با موفقیت ویرایش شد', 'data' => $educationalRecord]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش سوابق تحصیلی'], 500);
        }
    }

    public function destroyEducationalRecordPerson($id, Request $request)
    {
        $person = Person::findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();
            $educationalRecord = EducationalRecord::find($data['erID'])->delete();

            DB::commit();
            return response()->json(['message' => 'سوابق تحصیلی با موفقیت حذف شد']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در حذف سوابق تحصیلی'], 500);
        }
    }

    public function storeCourseRecordPerson($id, Request $request)
    {
        $person = Person::findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();
            $courseRecord = $this->courseRecordStore($data, $person->workForce->id);

            DB::commit();
            return response()->json(['message' => 'سوابق دوره ها با موفقیت ویرایش شد', 'data' => $courseRecord]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش سوابق دوره ها'], 500);
        }

    }

    public function updateCourseRecordPerson($id, Request $request)
    {
        $person = Person::findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();
            $courseRecord = CourseRecord::find($data['courseRecordID']);
            $data['workforceID'] = $person->workForce->id;
            $courseRecord = $this->courseRecordUpdate($courseRecord, $data);

            DB::commit();
            return response()->json(['message' => 'سوابق دوره ها با موفقیت ویرایش شد', 'data' => $courseRecord]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش سوابق دوره ها', 'error' => $e->getMessage()], 500);
        }

    }

    public function destroyCourseRecordPerson($id, Request $request)
    {
        $person = Person::findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();
            $courseRecord = CourseRecord::find($data['courseRecordID'])->delete();

            DB::commit();
            return response()->json(['message' => 'سوابق دوره ها با موفقیت حذف شد']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در حذف سوابق دوره ها'], 500);
        }

    }


    public function storeResumePerson($id, Request $request)
    {
        $person = Person::findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();
            $resume = $this->resumeStore($data, $person->workForce->id);

            DB::commit();
            return response()->json(['message' => 'رزومه ها با موفقیت ویرایش شد', 'data' => $resume]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در افزودن رزومه'], 500);
        }

    }

    public function updateResumePerson($id, Request $request)
    {
        $person = Person::findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();
            $resume = Resume::find($data['resumeID']);
            $data['workForceID'] = $person->workForce->id;
            $resume = $this->resumeUpdate($data, $resume);

            DB::commit();
            return response()->json(['message' => 'رزومه ها با موفقیت ویرایش شد', 'data' => $resume]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش رزومه', 'error' => $e->getMessage()], 500);
        }

    }

    public function destroyResumePerson($id, Request $request)
    {
        $person = Person::findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();
            $resume = Resume::find($data['resumeID'])->delete();

            DB::commit();
            return response()->json(['message' => 'رزومه ها با موفقیت حذف شد']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در حذف رزومه'], 500);
        }

    }

    public function storeMilitaryServicePerson($id, Request $request)
    {
        $person = Person::with('workForce')->findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();

            if (isset($data['militaryServiceID'])) {
                if ($data['hasMilitaryService'] == 0) {
                    $militaryService = MilitaryService::find($data['militaryServiceID']);
                    $militaryService->delete();
                    return response()->json(['message' => 'وضعیت نظام وظیفه با موفقیت حذف شد']);
                } else {
                    $militaryService = MilitaryService::find($data['militaryServiceID']);
                    $data['workForceID'] = $person->workForce->id;
                    $militaryService = $this->militaryServiceUpdate($militaryService, $data);
                }
            } elseif ($data['hasMilitaryService'] == 1) {
                $militaryService = $this->militaryServiceStore($data, $person->workForce->id);
            }

            DB::commit();
            return response()->json(['message' => 'وضعیت نظام وظیفه با موفقیت ویرایش شد']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در افزودن وضعیت نظام وظیفه'], 500);
        }

    }

    public function storeIsarPerson($id, Request $request)
    {
        $person = Person::with('workForce')->findOrFail($id);
        if ($person == null) {
            return response()->json(['message' => 'فردی با این مشخصات یافت نشد'], 404);
        }

        $data = $request->all();
        try {
            DB::beginTransaction();

            if (isset($data['isarID']) && $data['hasIsar'] == 0) {
                $isar = Isar::find($data['isarID']);
                $isar->delete();
                DB::commit();
                return response()->json(['message' => 'وضعیت نظام وظیفه با موفقیت حذف شد']);

            } elseif (isset($data['isarID'])) {
                $isar = Isar::find($data['isarID']);
                $data['workForceID'] = $person->workForce->id;
                $isar = $this->isarUpdate($isar, $data);
            } elseif ($data['hasIsar'] == 1) {
                $isar = $this->isarStore($data, $person->workForce->id);
            }

            DB::commit();
            return response()->json(['message' => 'وضعیت نظام وظیفه با موفقیت ویرایش شد']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ویرایش وضعیت نظام وظیفه', 'error' => $e->getMessage()], 500);
        }


    }

}
