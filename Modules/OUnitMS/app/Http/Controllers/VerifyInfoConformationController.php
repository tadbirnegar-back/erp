<?php

namespace Modules\OUnitMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Modules\AddressMS\app\Repositories\AddressRepository;
use Modules\FileMS\app\Http\Repositories\FileRepository;
use Modules\FileMS\app\Models\Extension;
use Modules\FileMS\app\Models\File;
use Modules\HRMS\app\Http\Repositories\EducationalRecordRepository;
use Modules\HRMS\app\Http\Repositories\RecruitmentScriptRepository;
use Modules\HRMS\app\Models\EducationalRecord;
use Modules\HRMS\app\Models\Employee;
use Modules\OUnitMS\app\Http\Traits\VerifyInfoRepository;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\TownOfc;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\OUnitMS\app\Notifications\VerifyInfoNotification;
use Modules\OUnitMS\app\Notifications\VerifyInfoSuccessNotification;
use Modules\PersonMS\app\Http\Repositories\PersonRepository;

class VerifyInfoConformationController extends Controller
{
    public array $data = [];
    use VerifyInfoRepository;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Show the specified resource.
     */
    public function show(): JsonResponse
    {
        $user = \Auth::user();
        $user->load('person.personable.homeAddress.village', 'person.personable.homeAddress.town.district.city.state.country', 'person.avatar', 'person.workForce.workforceable', 'person.workForce.educationalRecords.levelOfEducation');
        $workForce = $user->person->workForce;

        if ($workForce->workforceable_type === Employee::class) {
            /**
             * @var Employee $employee
             */
            $employee = $workForce->workForceable;
            $rs = $employee->recruitmentScripts()->whereHas('status', function ($query) {
                $query->where('name', 'فعال')
                    ->where('recruitment_script_status.create_date', function ($subQuery) {
                        $subQuery->selectRaw('MAX(create_date)')
                            ->from('recruitment_script_status')
                            ->whereColumn('recruitment_script_id', 'recruitment_scripts.id');
                    });
            })->with(['level', 'position', 'organizationUnit.unitable'])->get();
        }
        if (isset($rs)) {
            foreach ($rs as $ounit) {
                $ofc = $ounit->organizationUnit;
                $ofcType = $ofc->unitable_type;
                switch ($ofcType) {
                    case VillageOfc::class:
                        $ofc->load(['unitable.townOfc.organizationUnit', 'unitable.townOfc.districtOfc.organizationUnit', 'unitable.townOfc.districtOfc.cityOfc.organizationUnit', 'unitable.townOfc.districtOfc.cityOfc.stateOfc.organizationUnit']);
                        break;

                    case TownOfc::class:
                        $ofc->load(['unitable.districtOfc.organizationUnit', 'unitable.districtOfc.cityOfc.organizationUnit', 'unitable.districtOfc.cityOfc.stateOfc.organizationUnit']);
                        break;

                    case DistrictOfc::class:
                        $ofc->load(['unitable.cityOfc.organizationUnit', 'unitable.cityOfc.organizationUnit', 'unitable.cityOfc.stateOfc.organizationUnit']);
                        break;
                    case CityOfc::class:
                        $ofc->load(['unitable.stateOfc.organizationUnit']);
                        break;
                    default:
                        $ofc->load(['unitable']);
                        break;
                }
            }
        }
        $data = [
            'person' => $user->person,
            'recruitmentScripts' => $rs ?? null,

        ];
        return response()->json($data);
    }

    public function hasVerified()
    {
        $user = \Auth::user();
        $hasConfirmed = $this->userVerified($user);

        return response()->json(['hasConfirmed' => $hasConfirmed]);
    }

    public function verify()
    {
        $user = \Auth::user();

        $notif = $user->unreadNotifications()->where('type', '=', VerifyInfoNotification::class)->first();

        if (!is_null($notif) && !$notif->read()) {
            $notif->markAsRead();

        }
        $hasConfirmed = true;
        $user->notify(new VerifyInfoSuccessNotification());

        return response()->json(['hasConfirmed' => $hasConfirmed]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): JsonResponse
    {
        $user = \Auth::user();
        $data = $request->all();

        $validator = Validator::make($data, [
            'mobile' => [
                'required',
                'unique:users,mobile,' . $user->id,
            ],
            'nationalCode' => [
                'required',
                'unique:persons,national_code,' . $user->person->id,
            ],

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $user->mobile = $data['mobile'];
            $user->save();

            if ($request->isNewAddress) {
                $addressService = new AddressRepository();
                $data['userID'] = $user->id ?? null;
                $address = $addressService->store($data);

//                if ($address instanceof \Exception) {
//                    return response()->json(['message' => 'خطا در وارد کردن آدرس'], 500);
//                }

                $data['homeAddressID'] = $address->id;
//                $personService->naturalUpdate($data, $personResult->id);
            }
            $personService = new PersonRepository();
            $naturalResult = $personService->naturalUpdate($data, $data['naturalID']);

            $data['personID'] = $naturalResult->person->id;
            $uploadedFile = $request->file('file');

            if (!is_null($uploadedFile)) {
                $data['fileName'] = $uploadedFile->getClientOriginalName();
                $data['fileSize'] = $uploadedFile->getSize();
                $fileExtension = $uploadedFile->getClientOriginalExtension();
                $extension_id = Extension::where('name', '=', $fileExtension)->get(['id'])->first();

                if (is_null($extension_id)) {
                    return response()->json(['message' => 'فایل مجاز نمی باشد'], 400);
                }
                $data['extensionID'] = $extension_id->id;

                $file = FileRepository::store($uploadedFile, $data);
                if ($file instanceof File) {
                    $data['avatar'] = $file->id;
                }
                $personService->naturalUpdate($data, $naturalResult->id);

            }

            if (isset($data['recruitmentRecords'])) {
                $personEmployee = $naturalResult->person->workForce;
                $rs = json_decode($data['recruitmentRecords'], true);

                $rsRes = RecruitmentScriptRepository::bulkUpdate($rs, $personEmployee->workforceable_id);

                // Extract the organization unit IDs
                $ouIds = array_column($rs, 'ounitID');

                // Find all organization units in bulk
                $organizationUnits = OrganizationUnit::find($ouIds);

                // Update the head_id for each organization unit
                $organizationUnits->each(function ($ou) use ($user) {
                    $ou->head_id = $user->id;
                    $ou->save();
                });

                // Save all changes
//                OrganizationUnit::update($organizationUnits->toArray());
            }
            if (isset($data['deletedRecruitmentRecords'])) {
                $deletedRs = json_decode($data['deletedRecruitmentRecords'], true);

                $deleteRsResult = RecruitmentScriptRepository::delete($deletedRs);

            }

            if (isset($data['educationalRecords'])) {
//                if (!isset($personEmployee)) {
                $personEmployee = $naturalResult->person->workForce;

//                }
//                $edus = json_decode($data['educationalRecords'],true);

                $eduRes = EducationalRecordRepository::bulkUpdate($data['educationalRecords'], $personEmployee->id);
            }

            if (isset($data['deletedEducationalRecords'])) {
                $edus = json_decode($data['deletedEducationalRecords'], true);
                foreach ($edus as $id) {
                    EducationalRecord::find($id)->delete();
                }
            }


            $notif = $user->unreadNotifications()->where('type', '=', VerifyInfoNotification::class)->first();

            if (!is_null($notif) && !$notif->read()) {
                $notif->markAsRead();
                $user->notify(new VerifyInfoSuccessNotification());

            }
            DB::commit();

            return response()->json(['hasConfirmed' => true, 'message' => 'اطلاعات با موفقیت تکمیل و تایید شد']);


        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);

            return response()->json(['hasConfirmed' => false, 'message' => 'خطا در تایید اطلاعات'], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }
}
