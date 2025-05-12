<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Http\Traits\OtpTrait;
use Modules\AAA\app\Http\Traits\UserTrait;
use Modules\HRMS\app\Http\Enums\HireTypeEnum;
use Modules\HRMS\app\Http\Enums\PositionEnum;
use Modules\HRMS\app\Http\Enums\RecruitmentScriptStatusEnum;
use Modules\HRMS\app\Http\Traits\EmployeeTrait;
use Modules\HRMS\app\Http\Traits\MilitaryServiceTrait;
use Modules\HRMS\app\Models\HireType;
use Modules\HRMS\app\Models\Position;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Resources\VillageOunitListResource;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Resources\NaturalShowResource;
use Validator;

class RegisterEmployeeController extends Controller
{
    use PersonTrait, OtpTrait, UserTrait, EmployeeTrait, MilitaryServiceTrait;

    public function villagesList()
    {
        $result = OrganizationUnit::joinRelationship('village')
            ->with(['ancestors' => function ($query) {
                $query->where('unitable_type', '!=', StateOfc::class);
            }])
            ->where('unitable_type', VillageOfc::class)
            ->where('village_ofcs.hasLicense', true)
            ->select([
                'organization_units.id',
                'organization_units.parent_id',
                'organization_units.name as name',
                'village_ofcs.abadi_code as abadi_code',
            ])
            ->get();

        return VillageOunitListResource::collection($result);
    }

    public function districtsList()
    {
        $result = OrganizationUnit::with(['ancestors' => function ($query) {
                $query->where('unitable_type', '!=', StateOfc::class);
            }])
            ->where('unitable_type', DistrictOfc::class)
            ->select([
                'organization_units.id',
                'organization_units.parent_id',
                'organization_units.name as name',
            ])
            ->get();

        return VillageOunitListResource::collection($result);
    }

    public function checkPersonExistence(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'nationalCode' => 'required|string|min:10|max:10',
            'positionName' => 'required',
            'ounitID' => 'required|integer|exists:organization_units,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $person = $this->personExistenceCheckByNationalCode($data['nationalCode']);

        if (is_null($person)) {
            return response()->json([
                'type' => 'notFound',
            ]);
        }

        if (!is_null($person->employee_id)) {
            $activeRecruitmentScript = RecruitmentScript::query()->finalStatus()
                ->joinRelationship('position', function ($join) use ($data) {
                    $join->where('positions.name', $data['positionName']);
                })
                ->where('statuses.name', '=', RecruitmentScriptStatusEnum::ACTIVE->value)
                ->where('employee_id', $person->employee_id)
                ->where('organization_unit_id', $data['ounitID'])
                ->exists();
            if ($activeRecruitmentScript) {
                return response()->json([
                    'type' => 'alreadyExists',
                ]);
            }

        }
        if (!is_null($person->mobile)) {
            $otpData = [
                'mobile' => $person->mobile,
                'code' => mt_rand(10000, 99999),
                'expire' => 3,
            ];

            $this->sendOtp($otpData);

            return response()->json([
                'type' => 'otp',
                'person' => [
                    'mobile' => censorMobile($person->mobile),
                    'displayName' => $person->display_name,
                ],
                'message' => 'رمز یکبار مصرف ارسال شد']);
        } else {
            $person->load([
                'militaryService.militaryServiceStatus',
                'personLicenses.file.mimeType',
                'natural' => function ($query) {
                    $query->with(['religion', 'religionType']);
                }]);

            $person->natural->setAttribute('military', $person->militaryService->militaryServiceStatus);
            $person->natural->setAttribute('licenses', $person->personLicenses);

            return NaturalShowResource::make($person->natural)->additional([
                'type' => 'noMobile',
            ]);

        }

    }

    public function verifyPersonByOtp(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'nationalCode' => 'required|string|min:10|max:10',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            DB::beginTransaction();
            $person = $this->personExistenceCheckByNationalCode($data['nationalCode']);
            $otp = $this->verifyOtpByMobile($person->mobile, $data['otp']);
            if (is_null($otp)) {
                return response()->json([
                    'message' => 'کد تایید وارد شده نادرست می باشد',
                ], 403);
            }
            $otp->isUsed = true;
            $otp->save();

            DB::commit();
            $person->load([
                'personLicenses.file.mimeType',
                'militaryService.militaryServiceStatus',
                'natural' => function ($query) {
                    $query->with(['religion', 'religionType']);
                }]);
            $person->natural->setAttribute('military', $person->militaryService->militaryServiceStatus);
            $person->natural->setAttribute('licenses', $person->personLicenses);

            return NaturalShowResource::make($person->natural)->additional([
                'message' => 'کاربر با موفقیت تأیید شد',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->getTrace(),
            ], 500);
        }
    }

    public function RegisterEmployee(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'firstName' => ['required'],
            'lastName' => ['required'],
            'fatherName' => ['required'],
            'mobile' => ['required'],
            'birthDate' => ['sometimes'],
            'bcCode' => ['sometimes'],
            'gender' => ['required'],
            'bcIssueDate' => ['sometimes'],
            'bcIssueLocation' => ['sometimes'],
            'birthLocation' => ['sometimes'],
            'bcSerial' => ['sometimes'],
            'religionID' => ['required'],
            'religionTypeID' => ['sometimes'],
            'militaryServiceStatus' => ['sometimes'],
            'nationalCode' => ['required'],
            'ounitID' => ['required'],
            'positionName' => ['required'],
            'personLicenses' => ['required', 'json'],
            'scriptFiles' => ['required', 'json'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $userVerified = $this->userOtpVerifiedByDate($data['mobile'], now()->subHours(1));

        if (!$userVerified) {
            return response()->json([
                'message' => 'کاربر تایید نشده است',
            ], 403);
        }

        try {
            DB::beginTransaction();
            $p = $this->personExistenceCheckByNationalCode($data['nationalCode']);

            $data['dateOfBirth'] = convertJalaliPersianCharactersToGregorian($data['birthDate']);
            $data['bcIssueDate'] = convertJalaliPersianCharactersToGregorian($data['bcIssueDate']);

            $personResult = !is_null($p) ?
                $this->naturalUpdate($data, $p->natural) :
                $this->naturalStore($data);

            $data['personID'] = $personResult->person->id;
            $data['password'] = $data['nationalCode'];

            $personLicenses = json_decode($data['personLicenses'], true);

            $this->bulkStorePersonLicenses($personLicenses, $personResult->person->id);

            $this->militaryServiceStore($data, $personResult->person->id);

            $user = $personResult->person->user;
            $user = !is_null($user) ? $this->updateUser($data, $user) : $this->storeUser($data);


            $personAsEmployee = $this->isEmployee($data['personID']);
            $employee = !is_null($personAsEmployee) ? $this->employeeUpdate($data, $personAsEmployee) : $this->employeeStore($data);

            $ounit = OrganizationUnit::with('village')->find($data['ounitID']);

            $hireTypeEnum = HireTypeEnum::getHireTypeByOunit($ounit);

            $hireType = HireType::where('title', $hireTypeEnum->value)->first();

            $positionEnum = PositionEnum::tryFrom($data['positionName']);

            $position = Position::where('name', $positionEnum->value)->first();

//            $scriptType = ScriptType::where('title', $positionEnum->getScriptType()->value)->first();
//
//            $job = Job::where('title', $positionEnum->getJob()->value)->first();

            $data['files'] = $data['scriptFiles'];
            $data['hireTypeID'] = $hireType->id;
            $data['scriptTypeID'] = $position->script_type_id;
            $data['jobID'] = $position->job_id;
            $data['operatorID'] = $user->id;
            $data['positionID'] = $position->id;

            $pendingRsStatus = $this->pendingRsStatus();

            $rsRes = $this->rsSingleStore($data, $employee->id, $pendingRsStatus);


            DB::commit();
            return response()->json(['message' => 'با موفقیت ثبت شد'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage(), $e->getTrace()], 500);
        }

    }

}
