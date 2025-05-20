<?php

namespace Modules\PersonMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Modules\AAA\app\Http\Traits\OtpTrait;
use Modules\HRMS\app\Http\Enums\DependentStatusEnum;
use Modules\HRMS\app\Http\Enums\EducationalRecordStatusEnum;
use Modules\HRMS\app\Http\Enums\IsarStatusEnum;
use Modules\HRMS\app\Http\Enums\RelationTypeEnum;
use Modules\HRMS\app\Http\Traits\DependentTrait;
use Modules\HRMS\app\Http\Traits\EducationRecordTrait;
use Modules\HRMS\app\Http\Traits\IsarTrait;
use Modules\HRMS\app\Http\Traits\MilitaryServiceTrait;
use Modules\HRMS\app\Models\Dependent;
use Modules\HRMS\app\Models\EducationalRecord;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\Isar;
use Modules\HRMS\app\Resources\PersonListWithPositionAndRSList;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PersonMS\app\Http\Enums\PersonLicensesEnums;
use Modules\PersonMS\app\Http\Enums\PersonStatusEnum;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;
use Modules\PersonMS\app\Models\PersonLicense;
use Modules\PersonMS\app\Resources\NaturalShowResource;
use Validator;

class PersonLicenseController extends Controller
{
    use PersonTrait, MilitaryServiceTrait, DependentTrait, IsarTrait, EducationRecordTrait, OtpTrait;

    public function pendingIndex(Request $request)
    {

        $searchTerm = $request->name ?? null;
        $perPage = $request->perPage ?? 10;
        $pageNum = $request->pageNum ?? 1;
        $ounitID = $request->ounitID ?? null;
        $positionID = $request->positionID ?? null;

        $startDate = $request->has('startDate') ? convertJalaliPersianCharactersToGregorian($request->startDate) : null;
        $endDate = $request->has('endDate') ? convertJalaliPersianCharactersToGregorian($request->endDate) : null;

        if ($ounitID) {
            $ounit = OrganizationUnit::with(['descendantsAndSelf'])->find($ounitID);
            $ounitIDs = $ounit->descendantsAndSelf->pluck('id')->toArray();
        } else {
            $ounitIDs = null;
        }

        $pList = Employee::joinRelationship('workForce.person.natural', [
            'person' => function ($join) {
                $join->finalPersonStatus()
                    ->whereIn('statuses.name', [PersonStatusEnum::PENDING_TO_APPROVE->value]);
            }
        ])
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->SearchDisplayName($searchTerm);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('person_status.create_date', [$startDate, $endDate]);
            })
            ->when($positionID || $ounitIDs, function ($query) use ($ounitIDs, $positionID) {
                $query
                    ->joinRelationship('recruitmentScripts')
                    ->when($ounitIDs, function ($query) use ($ounitIDs) {
                        $query
                            ->whereIntegerInRaw('recruitment_scripts.organization_unit_id', $ounitIDs);
                    })
                    ->when($positionID, function ($query) use ($ounitIDs, $positionID) {
                        $query
                            ->where('recruitment_scripts.position_id', $positionID);
                    });
            })
            ->addSelect([
                'naturals.mobile',
                'naturals.gender_id',
                'persons.display_name',
                'persons.national_code',
                'person_status.create_date as last_updated',
            ])
            ->with(['recruitmentScripts' => function ($query) use ($ounitIDs) {
                $query
                    ->finalStatus()
                    ->join('positions', 'recruitment_scripts.position_id', '=', 'positions.id')
                    ->join('script_types', 'recruitment_scripts.script_type_id', '=', 'script_types.id')
                    ->select([
                        'recruitment_scripts.*',
                        'positions.name as position_name',
                        'script_types.title as script_type_title',
                        'statuses.name as status_name',
                        'statuses.class_name as status_class_name',
                    ])
                    ->with(['organizationUnit' => function ($query) {
                        $query->leftJoin('village_ofcs', function ($join) {
                            $join->on('village_ofcs.id', '=', 'organization_units.unitable_id')
                                ->where('unitable_type', '=', VillageOfc::class);
                        })
                            ->select([
                                'village_ofcs.abadi_code as abadi_code',
                                'organization_units.*'
                            ])
                            ->with(['ancestors' => function ($query) {
                                $query->where('unitable_type', '!=', StateOfc::class);
                            }]);
                    },]);
            }])
            ->paginate($perPage, page: $pageNum);

        return PersonListWithPositionAndRSList::collection($pList);
    }

    public function personInfoSummary(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'personID' => 'sometimes',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        $user = Auth::user();
        $personID = $data['personID'] ?? $user->person_id;

        $person = Person::with([
            'natural.spouse.latestStatus', 'avatar', 'latestStatus'])->find($personID);

        $childrenStatus = Dependent::where('main_person_id', $personID)->joinRelationship('status', function ($join) {
            $join->where('name', DependentStatusEnum::PENDING->value);
        })->exists();

        $isarStatus = Isar::where('person_id', $personID)->joinRelationship('status', function ($join) {
            $join->where('name', IsarStatusEnum::PENDING_APPROVE->value);
        })->exists();

        $educationStatus = EducationalRecord::where('person_id', $personID)->joinRelationship('status')
            ->addSelect([
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
            ])
            ->get();

        $personalInfoStatusObject = [
            'name' => $person->latestStatus->name,
            'className' => $person->latestStatus->class_name,
        ];

        $spouseInfoStatusObject = is_null($person->natural->isMarried) && is_null($person->natural->spouse) ? [
            'name' => PersonStatusEnum::PENDING_TO_FILL->value,
            'className' => PersonStatusEnum::PENDING_TO_FILL->getClassName(),

        ] : ($person->natural->isMarried != 1 ? $personalInfoStatusObject : [
            'name' => $person->natural?->spouse?->latestStatus->name,
            'className' => $person->natural?->spouse?->latestStatus->class_name,
        ]);

        $childrenInfoStatusObject = (!$childrenStatus) ? [
            'name' => DependentStatusEnum::ACTIVE->value,
            'className' => 'success'
        ] : [
            'name' => DependentStatusEnum::PENDING->value,
            'className' => 'primary'
        ];

        $isarInfoStatusObject = (!$isarStatus) ? [
            'name' => IsarStatusEnum::APPROVED->value,
            'className' => 'success'
        ] : [
            'name' => IsarStatusEnum::PENDING_APPROVE->value,
            'className' => 'primary'
        ];

        $educationInfoStatusObject = $educationStatus->isEmpty() ? [
            'name' => EducationalRecordStatusEnum::PENDING_TO_FILL->value,
            'className' => 'warning'
        ] : ($educationStatus->where('status_name', EducationalRecordStatusEnum::PENDING_APPROVE->value)->isNotEmpty()
            ? [
                'name' => EducationalRecordStatusEnum::PENDING_APPROVE->value,
                'className' => 'primary'
            ]
            : [
                'name' => EducationalRecordStatusEnum::APPROVED->value,
                'className' => 'success'
            ]);

        $result = [
            'person' => [
                'displayName' => $person->display_name,
                'avatar' => [
                    'slug' => $person->avatar?->slug,
                    'name' => $person->avatar?->name,
                    'size' => $person->avatar?->size,
//                    'type'=>$person->avatar->mimeType->name,
                ],
                'personnelCode' => '-',
            ],
            'statuses' => [
                'personalData' => $personalInfoStatusObject,
                'spouseData' => $spouseInfoStatusObject,
                'childrenData' => $childrenInfoStatusObject,
                'isarData' => $isarInfoStatusObject,
                'educationData' => $educationInfoStatusObject,
            ],
        ];

        return response()->json($result);


    }

    public function checkPersonExistence(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'nationalCode' => 'required|string|min:10|max:10',
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

        if ($person->natural->mobile) {
            $otpData = [
                'mobile' => $person->natural->mobile,
                'code' => mt_rand(10000, 99999),
                'expire' => 3,
            ];

            $this->sendOtp($otpData);
            return response()->json([
                'type' => 'otp',
                'person' => [
                    'mobile' => censorMobile($person->natural->mobile),
                    'displayName' => $person->display_name,
                ],
                'message' => 'رمز یکبار مصرف ارسال شد']);

        } else {
            $person->load([
                'personLicenses' => function ($query) {
                    $query->whereIn('license_type', [
                        PersonLicensesEnums::BIRTH_CERTIFICATE->value,
                        PersonLicensesEnums::MARRIAGE_PAGE->value,
                        PersonLicensesEnums::CHILDREN_PAGE->value,
                    ]);

                }
            ]);
            $person->natural->setAttribute('licenses', $person->personLicenses);
            $person->natural->setAttribute('national_code', $person->national_code);

            return NaturalShowResource::make($person->natural)->additional([
                'type' => 'found',
            ]);
        }

    }


    public function getPersonalData(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'personID' => 'sometimes',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        $user = Auth::user();
        $personID = $data['personID'] ?? $user->person_id;

        $person = Person::where('id', $personID)->with([
            'militaryService.militaryServiceStatus',
            'personLicenses.file.mimeType',
            'natural' => function ($query) {
                $query->with(['religion', 'religionType']);
            }])
            ->first();

        $person->natural->setAttribute('military', $person->militaryService?->militaryServiceStatus);
        $person->natural->setAttribute('licenses', $person->personLicenses);
        $person->natural->setAttribute('national_code', $person->national_code);

        return NaturalShowResource::make($person->natural);
    }

    public function updatePersonalData(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'firstName' => ['required'],
            'lastName' => ['required'],
            'fatherName' => ['required'],
            'birthDate' => ['sometimes'],
            'bcCode' => ['sometimes'],
            'gender' => ['required'],
            'bcIssueDate' => ['sometimes'],
            'bcIssueLocation' => ['sometimes'],
            'birthLocation' => ['sometimes'],
            'bcSerial' => ['sometimes'],
            'religionID' => ['sometimes'],
            'religionTypeID' => ['sometimes'],
            'militaryServiceStatus' => ['sometimes'],
            'personLicenses' => ['sometimes', 'json'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            $user = Auth::user();

            $data['personID'] = $data['personID'] ?? $user->person_id;
            $person = Person::with('natural')->find($data['personID']);

            $data['dateOfBirth'] = isset($data['birthDate']) ? convertJalaliPersianCharactersToGregorian($data['birthDate']) : null;
            $data['bcIssueDate'] = isset($data['bcIssueDate']) ? convertJalaliPersianCharactersToGregorian($data['bcIssueDate']) : null;

            $personResult = $this->naturalUpdate($data, $person->natural);

            if (isset($data['personLicenses'])) {
                $personLicenses = json_decode($data['personLicenses'], true);

                $this->bulkStorePersonLicenses($personLicenses, $person->id);
            }

            $this->militaryServiceStore($data, $person->id);

            DB::commit();
            return response()->json(['message' => 'با موفقیت ثبت شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage(), $e->getTrace()], 500);
        }
    }

    public function getSpouse(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'personID' => 'sometimes',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        $user = Auth::user();
        $personID = $data['personID'] ?? $user->person_id;

        $personNatural = Natural::joinRelationship('person', function ($join) use ($personID) {
            $join->where('persons.id', '=', $personID);
        })
            ->with(['spouse.natural' => function ($query) {
                $query->with(['religion', 'religionType']);

            }, 'spouse.personLicenses.file.mimeType'])
            ->first();

        $spouse = $personNatural->spouse;
        if ($spouse) {
            $spouseNatural = $spouse->natural;

            $spouseNatural->setAttribute('national_code', $spouse->national_code);

            $personNatural->setAttribute('spouse', $spouseNatural);

            $spouseNatural->setAttribute('licenses', $spouse->personLicenses);

        }
        if (is_null($personNatural->spouse)) {
            return response()->json([
                'data' => null,
                'isMarried' => !is_null($personNatural->isMarried) ? (bool)$personNatural->isMarried : null
            ]);
        }

        return NaturalShowResource::make($spouseNatural)
            ->additional(
                [
                    'isMarried' => !is_null($personNatural->isMarried) ? (bool)$personNatural->isMarried : null
                ]
            );

    }

    public function storeSpouse(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [

            'nationalCode' => ['sometimes'],
            'isMarried' => ['required'],
            'firstName' => ['sometimes'],
            'lastName' => ['sometimes'],
            'fatherName' => ['sometimes'],
            'birthDate' => ['sometimes'],
            'bcCode' => ['sometimes'],
            'gender' => ['sometimes'],
            'birthLocation' => ['sometimes'],
            'bcSerial' => ['sometimes'],
            'militaryServiceStatus' => ['sometimes'],
            'personLicenses' => ['sometimes', 'json'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            $user = Auth::user();

            $data['personID'] = $data['personID'] ?? $user->person_id;
            $person = Person::with('natural')->find($data['personID']);

            if ($data['isMarried'] == 1) {
                $data['dateOfBirth'] = isset($data['birthDate']) ? convertJalaliPersianCharactersToGregorian($data['birthDate']) : null;
                $data['bcIssueDate'] = isset($data['bcIssueDate']) ? convertJalaliPersianCharactersToGregorian($data['bcIssueDate']) : null;

                $spousePerson = Person::where('national_code', $data['nationalCode'])->first();

                $personResult = is_null($spousePerson) ? $this->naturalStore($data) : $this->naturalUpdate($data, $spousePerson->natural);

                $spouse = $personResult->person;
                if (isset($data['personLicenses'])) {
                    $personLicenses = json_decode($data['personLicenses'], true);

                    $this->bulkStorePersonLicenses($personLicenses, $spouse->id);
                }

                $n = $person->natural;
                $n->spouse_id = $spouse->id;
                $n->isMarried = true;
                $n->save();
            } else {
                $n = $person->natural;
                $n->isMarried = false;
                $n->spouse_id = null;
                $n->save();
            }

            $updateStatus = $this->updatedPersonStatus();
            $pendingStatus = $this->pendingToApprovePersonStatus();
            $person->status()->attach([$updateStatus->id, $pendingStatus->id]);
            DB::commit();
            return response()->json(['message' => 'با موفقیت ثبت شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage(), $e->getTrace()], 500);
        }


    }

    public function getChildren(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'personID' => 'sometimes',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        $user = Auth::user();
        $personID = $data['personID'] ?? $user->person_id;


        $children = Dependent::where('main_person_id', $personID)
            ->where('relation_type_id', RelationTypeEnum::CHILD->value)
            ->with(['relatedPerson.natural', 'relatedPerson.personLicenses.file.mimeType'])
            ->get();

        if ($children->isNotEmpty()) {
            $kids = $children->pluck('relatedPerson')->map(function ($kid) {
                $kidNatural = $kid->natural;

                $kidNatural->setAttribute('national_code', $kid->national_code);


                $kidNatural->setAttribute('licenses', $kid->personLicenses);

                return $kidNatural;
            });

            return NaturalShowResource::collection($kids);

        } else {
            return response()->json([]);
        }


    }

    public function storeChildren(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'firstName' => ['required'],
            'lastName' => ['required'],
            'nationalCode' => ['required'],
            'birthDate' => ['sometimes'],
            'bcCode' => ['sometimes'],
            'gender' => ['required'],
            'bcIssueDate' => ['sometimes'],
            'bcIssueLocation' => ['sometimes'],
            'birthLocation' => ['sometimes'],
            'bcSerial' => ['sometimes'],
            'religionID' => ['sometimes'],
            'religionTypeID' => ['sometimes'],
            'militaryServiceStatus' => ['sometimes'],
            'personLicenses' => ['sometimes', 'json'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            $user = Auth::user();

            $data['personID'] = $data['personID'] ?? $user->person_id;
            $person = Person::with('natural')->find($data['personID']);

            $data['fatherName'] = $person->natural->father_name;

            $data['dateOfBirth'] = isset($data['birthDate']) ? convertJalaliPersianCharactersToGregorian($data['birthDate']) : null;
            $data['bcIssueDate'] = isset($data['bcIssueDate']) ? convertJalaliPersianCharactersToGregorian($data['bcIssueDate']) : null;

            $heir = Person::where('national_code', $data['nationalCode'])->first();

            $personResult = is_null($heir) ? $this->naturalStore($data) : $this->naturalUpdate($data, $heir->natural);

            $child = $personResult->person;
            if (isset($data['personLicenses'])) {
                $personLicenses = json_decode($data['personLicenses'], true);

                $this->bulkStorePersonLicenses($personLicenses, $child->id);
            }
            $data['relatedPersonID'] = $child->id;
            $this->storeDependent($data, $person);

            $updateStatus = $this->updatedPersonStatus();
            $pendingStatus = $this->pendingToApprovePersonStatus();
            $person->status()->attach([$updateStatus->id, $pendingStatus->id]);
            DB::commit();
            return response()->json(['message' => 'با موفقیت ثبت شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage(), $e->getTrace()], 500);
        }


    }

    public function updateChild(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'firstName' => ['required'],
            'lastName' => ['required'],
            'nationalCode' => ['required'],
            'birthDate' => ['sometimes'],
            'bcCode' => ['sometimes'],
            'gender' => ['required'],
            'bcIssueDate' => ['sometimes'],
            'bcIssueLocation' => ['sometimes'],
            'birthLocation' => ['sometimes'],
            'bcSerial' => ['sometimes'],
            'religionID' => ['sometimes'],
            'religionTypeID' => ['sometimes'],
            'militaryServiceStatus' => ['sometimes'],
            'personLicenses' => ['sometimes', 'json'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            $user = Auth::user();

            $data['personID'] = $data['personID'] ?? $user->person_id;

            $child = Dependent::joinRelationship('relatedPerson', function ($join) use ($data) {
                $join->where('persons.national_code', $data['nationalCode']);
            })
                ->where('main_person_id', $data['personID'])
                ->with('relatedPerson.natural')
                ->first();
            $person = Person::with('natural')->find($data['personID']);

            $data['fatherName'] = $person->natural->father_name;

            $data['dateOfBirth'] = isset($data['birthDate']) ? convertJalaliPersianCharactersToGregorian($data['birthDate']) : null;
            $data['bcIssueDate'] = isset($data['bcIssueDate']) ? convertJalaliPersianCharactersToGregorian($data['bcIssueDate']) : null;

            $personResult = $this->naturalUpdate($data, $child->relatedPerson->natural);
            $child = $personResult->person;
            if (isset($data['personLicenses'])) {
                $personLicenses = json_decode($data['personLicenses'], true);

                $this->bulkStorePersonLicenses($personLicenses, $child->id);
            }
            $updateStatus = $this->updatedPersonStatus();
            $pendingStatus = $this->pendingToApprovePersonStatus();
            $person->status()->attach([$updateStatus->id, $pendingStatus->id]);
            DB::commit();
            return response()->json(['message' => 'با موفقیت ثبت شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage(), $e->getTrace()], 500);
        }
    }

    public function getIsar(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'personID' => 'sometimes',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        $user = Auth::user();
        $personID = $data['personID'] ?? $user->person_id;

        $isar = Isar::where('person_id', $personID)->with('isarStatus')->first();

        $isarCard = PersonLicense::where('license_type', PersonLicensesEnums::ISAR->value)
            ->where('person_id', $personID)
            ->with('file')
            ->first();

        return response()->json([
            'isarType' => $isar?->isarStatus,
            'isarCard' => !is_null($isarCard) ? [
                'id' => $isarCard->id,
                'file' => [
                    'id' => $isarCard->file->id,
                    'name' => $isarCard->file->name,
                    'slug' => $isarCard->file->slug,
                    'size' => $isarCard->file->size,
                    'type' => $isarCard->file->mimeType->name,
                ],
                'licenseType' => [
                    'id' => $isarCard->license_type,
                    'name' => $isarCard->license_type->name(),
                ],
            ] : null,
        ]);
    }

    public function updateIsar(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'isarStatusID' => 'required',
            'personLicenses' => ['required', 'json'],
            'personID' => 'sometimes',
        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $personID = $data['personID'] ?? $user->person_id;
            $person = Person::find($personID);
            $this->isarStore($data, $personID);

            if (isset($data['personLicenses'])) {
                $personLicenses = json_decode($data['personLicenses'], true);

                $this->bulkStorePersonLicenses($personLicenses, $personID);
            }
            $updateStatus = $this->updatedPersonStatus();
            $pendingStatus = $this->pendingToApprovePersonStatus();
            $person->status()->attach([$updateStatus->id, $pendingStatus->id]);

            DB::commit();

            return response()->json(['message' => 'با موفقیت اضافه شد.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage(), $e->getTrace()], 500);
        }


    }

    public function getEducationalRecords(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'personID' => 'sometimes',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        $user = Auth::user();
        $personID = $data['personID'] ?? $user->person_id;

        $eduRecords = EducationalRecord::where('person_id', $personID)
            ->with(['levelOfEducation', 'attachments.mimeType'])
            ->get();

        $result = $eduRecords->map(function ($eduRecord) {
            return [
                'id' => $eduRecord->id,
                'universityName' => $eduRecord->university_name,
                'fieldOfStudy' => $eduRecord->field_of_study,
                'startDate' => !is_null($eduRecord->start_date) ? convertGregorianToJalali($eduRecord->start_date) : null,
                'endDate' => !is_null($eduRecord->end_date) ? convertGregorianToJalali($eduRecord->end_date) : null,
                'average' => $eduRecord->average,
                'levelOfEducational' => $eduRecord->levelOfEducation,
                'attachments' => $eduRecord->attachments->map(function ($attachment) {
                    return [
                        'id' => $attachment->pivot->id,
                        'title' => $attachment->pivot->title,
                        'slug' => $attachment->slug,
                        'size' => $attachment->size,
                        'type' => $attachment->mimeType->name,

                    ];
                })->first(),
            ];
        });

        return response()->json($result);
    }

    public function insertEducationalRecord(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [

            'universityName' => 'required',
            'fieldOfStudy' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'average' => 'required',
            'levelOfEducationalID' => 'required',
            'files' => ['required', 'json'],
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        $user = Auth::user();
        $personID = $data['personID'] ?? $user->person_id;
        $person = Person::find($personID);

        try {
            DB::beginTransaction();
            $this->EducationalRecordSingleStore($data, $personID);

            $updateStatus = $this->updatedPersonStatus();
            $pendingStatus = $this->pendingToApprovePersonStatus();

            $person->status()->attach([$updateStatus->id, $pendingStatus->id]);
            DB::commit();

            return response()->json(['message' => 'با موفقیت ثبت شد']);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                $e->getMessage(),
                $e->getTrace(),

            ]);
        }


    }

    public function updateEducationalRecord(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'erID' => 'required',
            'universityName' => 'required',
            'fieldOfStudy' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'average' => 'required',
            'levelOfEducationalID' => 'required',
            'files' => ['required', 'json'],
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        $user = Auth::user();
        $personID = $data['personID'] ?? $user->person_id;
        try {
            $er = EducationalRecord::find($data['erID']);
            DB::beginTransaction();
            $this->EducationalRecordSingleUpdate($data, $er);
            $person = Person::find($personID);

            $updateStatus = $this->updatedPersonStatus();
            $pendingStatus = $this->pendingToApprovePersonStatus();
            $person->status()->attach([$updateStatus->id, $pendingStatus->id]);
            DB::commit();

            return response()->json(['message' => 'با موفقیت ثبت شد']);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                $e->getMessage(),
                $e->getTrace(),

            ]);
        }


    }
}
