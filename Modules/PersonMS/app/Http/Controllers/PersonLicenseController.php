<?php

namespace Modules\PersonMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Modules\HRMS\app\Http\Enums\RelationTypeEnum;
use Modules\HRMS\app\Http\Traits\DependentTrait;
use Modules\HRMS\app\Http\Traits\EducationRecordTrait;
use Modules\HRMS\app\Http\Traits\IsarTrait;
use Modules\HRMS\app\Http\Traits\MilitaryServiceTrait;
use Modules\HRMS\app\Models\Dependent;
use Modules\HRMS\app\Models\EducationalRecord;
use Modules\HRMS\app\Models\Isar;
use Modules\PersonMS\app\Http\Enums\PersonLicensesEnums;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;
use Modules\PersonMS\app\Models\PersonLicense;
use Modules\PersonMS\app\Resources\NaturalShowResource;
use Validator;

class PersonLicenseController extends Controller
{
    use PersonTrait, MilitaryServiceTrait, DependentTrait, IsarTrait, EducationRecordTrait;

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

        $person = Person::with(['natural', 'avatar'])->find($personID);

        $natural = $person->natural;

        $requiredColumns = [
            'first_name',
            'last_name',
            'father_name',
            'birth_date',
            'bc_code',
            'gender_id',
            'bc_issue_date',
            'bc_issue_location',
            'birth_location',
            'bc_serial',
            'religion_id',
        ];

        $filledCount = count(array_filter($requiredColumns, function ($column) use ($natural) {
            return !is_null($natural->{$column}) && $natural->{$column} !== '';
        }));

        if ($filledCount == count($requiredColumns)) {
            $personalStatus = true;
        } else {
            $personalStatus = false;
        }

        if ($natural->isMarried == 1 && $natural->spouse_id != null) {
            $spouseStatus = true;
        } elseif ($natural->isMarried == 0) {
            $spouseStatus = false;
        } else {
            $spouseStatus = null;
        }

        $childrenStatus = true;
        $isarStatus = true;
        $educationStatus = $person->educationalRecords()->count() > 0;

        $personalInfoStatusObject = $personalStatus ? [
            'name' => 'تکمیل شده',
            'className' => 'success'
        ] : [
            'name' => 'در انتظار تکمیل',
            'className' => 'warning'
        ];

        $spouseInfoStatusObject = $spouseStatus ? [
            'name' => 'تکمیل شده',
            'className' => 'success'
        ] : (is_null($spouseStatus) ? [
            'name' => 'در انتظار تکمیل',
            'className' => 'warning'
        ] : [
            'name' => 'تکمیل شده',
            'className' => 'success'
        ]);

        $childrenInfoStatusObject = $childrenStatus ? [
            'name' => 'تکمیل شده',
            'className' => 'success'
        ] : [
            'name' => 'در انتظار تکمیل',
            'className' => 'warning'
        ];

        $isarInfoStatusObject = $isarStatus ? [
            'name' => 'تکمیل شده',
            'className' => 'success'
        ] : [
            'name' => 'در انتظار تکمیل',
            'className' => 'warning'
        ];

        $educationInfoStatusObject = $educationStatus ? [
            'name' => 'تکمیل شده',
            'className' => 'success'
        ] : [
            'name' => 'در انتظار تکمیل',
            'className' => 'warning'
        ];

        $result = [
            'person' => [
                'displayName' => $person->display_name,
                'avatar' => [
                    'slug' => $person->avatar->slug,
                    'name' => $person->avatar->name,
                    'size' => $person->avatar->size,
//                    'type'=>$person->avatar->mimeType->name,
                ]
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
        $person->load([
            'militaryService.militaryServiceStatus',
            'personLicenses.file.mimeType',
            'natural' => function ($query) {
                $query->with(['religion', 'religionType']);
            }]);

        $person->natural->setAttribute('licenses', $person->personLicenses);

        return NaturalShowResource::make($person->natural)->additional([
            'type' => 'found',
        ]);
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

        $person->natural->setAttribute('military', $person->militaryService->militaryServiceStatus);
        $person->natural->setAttribute('licenses', $person->personLicenses);

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

            }])
            ->first();
        $spouse = $personNatural->spouse;
        if ($spouse) {
            $spouseNatural = $spouse->natural;
            $spouseNatural->setAttribute('national_code', $spouse->national_code);
            $personNatural->setAttribute('spouse', $spouseNatural);

        }
        return NaturalShowResource::make($personNatural);

    }

    public function storeSpouse(Request $request)
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

            if ($data['isMarried'] == 1) {
                $data['dateOfBirth'] = isset($data['birthDate']) ? convertJalaliPersianCharactersToGregorian($data['birthDate']) : null;
                $data['bcIssueDate'] = isset($data['bcIssueDate']) ? convertJalaliPersianCharactersToGregorian($data['bcIssueDate']) : null;

                $personResult = $this->naturalStore($data);
                $spouse = $personResult->person;
                if (isset($data['personLicenses'])) {
                    $personLicenses = json_decode($data['personLicenses'], true);

                    $this->bulkStorePersonLicenses($personLicenses, $spouse->id);
                }
                $n = $person->natural;
                $n->spouse_id = $personResult->id;
                $n->isMarried = true;
                $n->save();
            } else {
                $n = $person->natural;
                $n->isMarried = false;
                $n->spouse_id = null;
                $n->save();
            }

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

        $children = Person::with(['heirs' => function ($query) {
            $query->where('relation_type_id', RelationTypeEnum::CHILD->value)
                ->with(['natural', 'personLicenses.file']);
        }])->find($personID);

        $result = $children->heirs->map(function ($heir) {
            return [
                'id' => $heir->id,
                'name' => $heir->display_name,
                'nationalCode' => $heir->national_code,
                'birthDate' => !is_null($heir->natural->birth_date) ? convertGregorianToJalali($heir->natural->birth_date) : null,
                'birthLocation' => $heir->birth_location,
                'bcCode' => $heir->bc_code,
                'gender' => [
                    'id' => $heir->gender_id,
                    'name' => $heir->gender_id == 1 ? 'مرد' : 'زن',
                ],
                'licenses' => $heir->personLicenses->map(function ($license) {
                    return [
                        'id' => $license->id,
                        'licenseType' => [
                            'id' => $license->license_type,
                            'name' => $license->license_type->name(),
                        ],
                        'file' => [
                            'id' => $license->file->id,
                            'name' => $license->file->name,
                            'slug' => $license->file->slug,
                            'size' => $license->file->size,
                            'type' => $license->file->mimeType->name,
                        ],
                    ];
                })
            ];
        });


        return response()->json($result);

    }

    public function storeChildren(Request $request)
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

            $personResult = $this->naturalStore($data);
            $child = $personResult->person;
            if (isset($data['personLicenses'])) {
                $personLicenses = json_decode($data['personLicenses'], true);

                $this->bulkStorePersonLicenses($personLicenses, $child->id);
            }
            $data['relatedPersonID'] = $child->id;
            $this->storeDependent($data, $person);


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
            'heirID' => ['required'],
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
            $child = Dependent::where('id', $data['heirID'])
                ->where('main_person_id', $data['personID'])
                ->with('relatedPerson.natural')
                ->first();
            $person = Person::with('natural')->find($data['personID']);

            $data['dateOfBirth'] = isset($data['birthDate']) ? convertJalaliPersianCharactersToGregorian($data['birthDate']) : null;
            $data['bcIssueDate'] = isset($data['bcIssueDate']) ? convertJalaliPersianCharactersToGregorian($data['bcIssueDate']) : null;

            $personResult = $this->naturalUpdate($data, $child->relatedPerson->natural);
            $child = $personResult->person;
            if (isset($data['personLicenses'])) {
                $personLicenses = json_decode($data['personLicenses'], true);

                $this->bulkStorePersonLicenses($personLicenses, $child->id);
            }
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
            'isarType' => $isar?->isar_status,
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
            'isarStatusID' => 'sometimes',
            'relativeTypeID' => 'sometimes',
            'length' => 'sometimes',
            'percentage' => 'sometimes',
            'personID' => 'sometimes',
        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $personID = $data['personID'] ?? $user->person_id;

            $this->isarStore($data, $personID);

            if (isset($data['personLicenses'])) {
                $personLicenses = json_decode($data['personLicenses'], true);

                $this->bulkStorePersonLicenses($personLicenses, $personID);
            }
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
                        'id' => $attachment->attch_id,
                        'title' => $attachment->pivot_title,
                        'slug' => $attachment->slug,
                        'size' => $attachment->size,
                        'type' => $attachment->mimeType->name,

                    ];
                }),
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
            'levelOfEducational' => 'required',
            'files' => ['required', 'json'],
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        $user = Auth::user();
        $personID = $data['personID'] ?? $user->person_id;
        try {
            DB::beginTransaction();
            $this->EducationalRecordSingleStore($data, $personID);
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
            'levelOfEducational' => 'required',
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
            $this->EducationalRecordUpdate($data, $er);

            $files = json_decode($data['files'], true);
            $this->attachment($files, $er);

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
