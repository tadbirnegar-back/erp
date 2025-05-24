<?php

namespace Modules\PersonMS\app\Http\Traits;

use Modules\HRMS\app\Http\Enums\DependentStatusEnum;
use Modules\HRMS\app\Http\Enums\EducationalRecordStatusEnum;
use Modules\HRMS\app\Http\Enums\IsarStatusEnum;
use Modules\HRMS\app\Models\Dependent;
use Modules\HRMS\app\Models\EducationalRecord;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\Isar;
use Modules\HRMS\app\Models\MilitaryService;
use Modules\PersonMS\app\Http\Enums\PersonLicensesEnums;
use Modules\PersonMS\app\Http\Enums\PersonLicenseStatusEnum;
use Modules\PersonMS\app\Http\Enums\PersonStatusEnum;
use Modules\PersonMS\app\Models\Legal;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;
use Modules\PersonMS\app\Models\PersonLicense;
use Modules\StatusMS\app\Models\Status;

trait PersonTrait
{
    public function naturalPersonExists(string $nationalCode): Person|null
    {
        $person = Person::where('personable_type', Natural::class)
            ->where('national_code', '=', $nationalCode)
            ->with('user', 'personable.homeAddress.city.state.country', 'avatar', 'status')
            ->first();


        return $person;
    }

    public function personExistenceCheckByNationalCode(string $nationalCode)
    {
        $person = Person::where('national_code', $nationalCode)
            ->leftJoinRelationship('workForce', function ($join) {
                $join->where('workforceable_type', Employee::class);
            })
            ->joinRelationship('natural')
            ->addSelect([
                'work_forces.workforceable_id as employee_id',
                'naturals.mobile as mobile',
            ])->first();

        return $person;
    }

    public function optionalPersonAndNatualAndLegalUpdate(array $data, Person $person)
    {


        if ($person->personable_type == Natural::class) {
            $lastNatural = Natural::where('id', $person->personable_id)->first();
            $lastNatural->update([
                'first_name' => $data['firstName'] ?? $lastNatural->first_name,
                'last_name' => $data['lastName'] ?? $lastNatural->last_name,
                'mobile' => $data['mobile'] ?? $lastNatural->mobile,

            ]);
            $person->update([
                'display_name' => (isset($data['firstName']) && isset($data['lastName'])) ? ($data['firstName'] . ' ' . $data['lastName']) : $person->display_name,
                'national_code' => $data['nationalCode'] ?? $person->national_code,
                'phone' => $data['phone'] ?? $person->phone,
            ]);

        } else {
            $lastLegal = Legal::where('id', $person->personable_id)->first();
            $lastLegal->update([
                'name' => $data['name'] ?? $person->display_name,
            ]);
            $person->update([
                'display_name' => $data['name'] ?? $person->display_name,
                'phone' => $data['phone'] ?? $person->phone,
            ]);

        }
    }

    public function naturalStore(array $data): Natural
    {

        $naturalPerson = new Natural();
        $naturalPerson->fill([
            'first_name' => convertToDbFriendly($data['firstName']),
            'last_name' => convertToDbFriendly($data['lastName']),
            'mobile' => $data['mobile'] ?? null,
            'father_name' => $data['fatherName'] ?? null,
            'birth_date' => $data['dateOfBirth'] ?? null,
            'bc_code' => $data['bcCode'] ?? null,
            'isMarried' => $data['isMarried'] ?? null,
            'gender_id' => $data['gender'] ?? 1,
            'bc_issue_date' => $data['bcIssueDate'] ?? null,
            'bc_issue_location' => $data['bcIssueLocation'] ?? null,
            'birth_location' => $data['birthLocation'] ?? null,
            'bc_serial' => $data['bcSerial'] ?? null,
            'religion_id' => $data['religionID'] ?? null,
            'religion_type_id' => $data['religionTypeID'] ?? null,
            'spouse_id' => $data['spouseID'] ?? null,
        ]);


        $naturalPerson->save();

        $person = new Person();
        $person->fill([
            'display_name' => $naturalPerson->first_name . ' ' . $naturalPerson->last_name,
            'national_code' => convertToDbFriendly($data['nationalCode']),
            'profile_picture_id' => $data['avatar'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);

        $naturalPerson->person()->save($person);

        $createStatus = $this->caseCreatePersonStatus();
        $pendingToFillStatus = $this->pendingToFillPersonStatus();

        $naturalPerson->person->status()->attach([$createStatus->id, $pendingToFillStatus->id]);


        return $naturalPerson;


    }

    public function activePersonStatus()
    {
        return Person::GetAllStatuses()->firstWhere('name', '=', 'فعال');
    }

    public function legalExists(string $name)
    {

        $result = Legal::with('address.city.state.country', 'person.statuses', 'person.avatar')->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", $name)->get();

        return $result;


    }

    public function legalStore(array $data)
    {


        $legal = new Legal();
        $legal->name = $data['name'];
        $legal->registration_number = $data['registrationNumber'] ?? null;
        $legal->foundation_date = $data['foundationDate'] ?? null;
        $legal->legal_type_id = $data['legalTypeID'] ?? null;
        $legal->address_id = $data['businessAddressID'] ?? null;
        $legal->save();

        $person = new Person();
        $person->display_name = $legal->name;
        $person->national_code = $data['nationalCode'] ?? null;
        $person->profile_picture_id = $data['avatar'] ?? null;
        $person->phone = $data['phone'] ?? null;

        $legal->person()->save($person);
        $status = $this->activePersonStatus();
        $legal->person->status()->attach($status->id);


        return $legal;


    }

    public function naturalUpdate(array $data, Natural $naturalPerson)
    {


        $naturalPerson->fill([
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'mobile' => $data['mobile'] ?? $naturalPerson->person->user?->mobile,
            'father_name' => $data['fatherName'] ?? null,
            'birth_date' => $data['dateOfBirth'] ?? null,
            'bc_code' => $data['bcCode'] ?? null,
            'isMarried' => $data['isMarried'] ?? $naturalPerson->isMarried,
            'gender_id' => $data['gender'],
            'bc_issue_date' => $data['bcIssueDate'] ?? null,
            'bc_issue_location' => $data['bcIssueLocation'] ?? null,
            'birth_location' => $data['birthLocation'] ?? null,
            'bc_serial' => $data['bcSerial'] ?? null,
            'religion_id' => $data['religionID'] ?? null,
            'religion_type_id' => $data['religionTypeID'] ?? null,
        ]);

        $naturalPerson->save();

        $person = $naturalPerson->person;
        $person->display_name = $naturalPerson->first_name . ' ' . $naturalPerson->last_name;
        $person->national_code = $data['nationalCode'] ?? $person->national_code;
        $person->profile_picture_id = $data['avatar'] ?? $person->profile_picture_id;

        $naturalPerson->person()->save($person);
//        if ($person->latestStatus->name == PersonStatusEnum::PENDING_TO_FILL->value || $person->latestStatus->name == PersonStatusEnum::PENDING_TO_APPROVE->value) {
        $updateStatus = $this->updatedPersonStatus();
        $pendingStatus = $this->pendingToApprovePersonStatus();
        $naturalPerson->person->statuses()->attach([$updateStatus->id, $pendingStatus->id]);
//        }

        return $naturalPerson;

    }

    public function personNaturalUpdate(array $data, Person $person)
    {
        $naturalPerson = $person->personable;

        $naturalPerson->fill([
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'mobile' => $data['mobile'] ?? null,
            'phone_number' => $data['phoneNumber'] ?? null,
            'father_name' => $data['fatherName'] ?? null,
            'birth_date' => $data['dateOfBirth'] ?? null,
            'bc_code' => $data['bcCode'] ?? null,
            'job' => $data['job'] ?? null,
            'isMarried' => $data['isMarried'] ?? null,
            'level_of_spouse_education' => $data['levelOfSpouseEducation'] ?? null,
            'spouse_first_name' => $data['spouseFirstName'] ?? null,
            'spouse_last_name' => $data['spouseLastName'] ?? null,
            'home_address_id' => $data['homeAddressID'] ?? null,
            'job_address_id' => $data['jobAddressID'] ?? null,
            'gender_id' => $data['gender'],
            'bc_issue_date' => $data['bcIssueDate'] ?? null,
            'bc_issue_location' => $data['bcIssueLocation'] ?? null,
            'birth_location' => $data['birthLocation'] ?? null,
            'bc_serial' => $data['bcSerial'] ?? null,
            'religion_id' => $data['religionID'] ?? null,
            'religion_type_id' => $data['religionTypeID'] ?? null,
        ]);

        $naturalPerson->save();

        $person->display_name = $naturalPerson->first_name . ' ' . $naturalPerson->last_name;
        $person->national_code = $data['nationalCode'];
        $person->profile_picture_id = $data['avatar'] ?? null;

        $naturalPerson->person()->save($person);
        $statusID = $person->status;
        if (isset($data['statusID']) && $statusID[0]->id != $data['statusID']) {
            $person->status()->attach($data['statusID']);
        }

        return $person;
    }

    public function legalUpdate(array $data, Legal $legal)
    {


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
        $status = $this->activePersonStatus();
        $legal->person->status()->attach($status->id);

        return $legal;


    }

    public function latestScriptByNationalCode(string $nationalCode)
    {
        return Person::where('national_code', $nationalCode)->whereHas('latestRecruitmentScript', function ($query) {

            $query->where('expire_date', '>', now())
                ->whereDoesntHave('latestStatus', function ($query) {
                    $query->where('name', '=', 'غیرفعال');
                });

        })->with(['latestRecruitmentScript.issueTime'])->first();
    }

    public function personUpdateOrInsert($data)
    {
        if ($data->personType == 1) {
            $person = Person::where('personable_type', Natural::class)->where('national_code', $data->nationalCode)->first();
        } else {
            $person = Person::where('personable_type', Legal::class)->where('national_code', $data->nationalCode)->first();
        }

        if ($person) {
            if ($person->personable_type == Natural::class) {
                $natural = Natural::where('id', $person->personable_id)->first();
                $natural->first_name = $natural->first_name == null ? ($data->first_name ?? null) : $natural->first_name;
                $natural->last_name = $natural->last_name == null ? ($data->last_name ?? null) : $natural->last_name;
                $natural->mobile = $natural->mobile == null ? ($data->mobile ?? null) : $natural->mobile;
                $natural->phone_number = $natural->phone_number == null ? ($data->phone_number ?? null) : $natural->phone_number;
                $natural->father_name = $natural->father_name == null ? ($data->father_name ?? null) : $natural->father_name;
                $natural->birth_date = $natural->birth_date == null ? (convertPersianToGregorianBothHaveTimeAndDont($data->birth_date) ?? null) : $natural->birth_date;
                $natural->bc_code = $natural->bc_code == null ? ($data->bc_code ?? null) : $natural->bc_code;
                $natural->job = $natural->job == null ? ($data->job ?? null) : $natural->job;
                $natural->isMarried = $natural->isMarried == null ? ($data->is_married ?? null) : $natural->isMarried;
                $natural->level_of_spouse_education = $natural->level_of_spouse_education == null ? ($data->level_of_spouse_education ?? null) : $natural->level_of_spouse_education;
                $natural->spouse_first_name = $natural->spouse_first_name == null ? ($data->spouse_first_name ?? null) : $natural->spouse_first_name;
                $natural->spouse_last_name = $natural->spouse_last_name == null ? ($data->spouse_last_name ?? null) : $natural->spouse_last_name;
                $natural->home_address_id = $natural->home_address_id == null ? ($data->home_address_id ?? null) : $natural->home_address_id;
                $natural->job_address_id = $natural->job_address_id == null ? ($data->job_address_id ?? null) : $natural->job_address_id;
                $natural->gender_id = $natural->gender_id == null ? ($data->gender_id ?? null) : $natural->gender_id;
                $natural->bc_issue_date = $natural->bc_issue_date == null ? (convertPersianToGregorianBothHaveTimeAndDont($data->bc_issue_date) ?? null) : $natural->bc_issue_date;
                $natural->bc_issue_location = $natural->bc_issue_location == null ? ($data->bc_issue_location ?? null) : $natural->bc_issue_location;
                $natural->birth_location = $natural->birth_location == null ? ($data->birth_location ?? null) : $natural->birth_location;
                $natural->bc_serial = $natural->bc_serial == null ? ($data->bc_serial ?? null) : $natural->bc_serial;
                $natural->religion_id = $natural->religion_id == null ? ($data->religion_id ?? null) : $natural->religion_id;
                $natural->religion_type_id = $natural->religion_type_id == null ? ($data->religion_type_id ?? null) : $natural->religion_type_id;
                $natural->save();

                $person->display_name = $person->display_name == null ? ($natural->first_name ? ($natural->first_name . ' ' . $natural->last_name) : $person->display_name) : $person->display_name;
                $person->national_code = $person->national_code == null ? $data->nationalCode : $person->national_code;
                $person->profile_picture_id = $person->profile_picture_id == null ? (isset($data->avatar) ? $data->avatar : null) : $person->profile_picture_id;
                $person->phone = $person->phone == null ? ($data->phone ?? null) : $person->phone;
                $person->signature_file_id = $person->signature_file_id == null ? $data->signatureFile : $person->signature_file_id;
                $person->save();


                if ($natural->gender_id == 1) {
                    $militaryService = MilitaryService::where('person_id', $person->id)->first();
                    if ($militaryService) {
                        $newMilitaryService = MilitaryService::where('person_id', $person->id)->first();
                        $newMilitaryService->exemption_type_id = ($militaryService->exemptionType == null) ? ($data->exemptionTypeID ?? null) : $militaryService->exemptionType->id;
                        $newMilitaryService->military_service_status_id = ($militaryService->militaryServiceStatus == null) ? ($data->militaryServiceStatusID ?? null) : $militaryService->militaryServiceStatus->id;
                        $newMilitaryService->work_force_id = null;
                        $newMilitaryService->issue_date = (is_null($militaryService->issue_date)) ? (isset($data->issueDate) ? (convertPersianToGregorianBothHaveTimeAndDont($data->issueDate) ?? null) : null) : $militaryService->issue_date;
                        $newMilitaryService->person_id = $person->id;
                        $newMilitaryService->save();
                    } else {
                        $militaryService = new MilitaryService();
                        $militaryService->exemption_type_id = ($data->exemptionTypeID ?? null);
                        $militaryService->military_service_status_id = ($data->militaryServiceStatusID ?? null);
                        $militaryService->work_force_id = null;
                        $militaryService->issue_date = isset($data->issueDate) ? convertPersianToGregorianBothHaveTimeAndDont($data->issueDate) : null;
                        $militaryService->person_id = $person->id;
                        $militaryService->save();
                    }
                }

            } else {
                $legal = Legal::where('id', $person->personable_id)->first();
                $legal->name = $legal->name == null ? ($data['name'] ?? $legal->name) : $legal->name;
                $legal->registration_number = $legal->registration_number == null ? ($data['registration_number'] ?? $legal->registration_number) : $legal->registration_number;
                $legal->foundation_date = $legal->foundation_date == null ? ($data['foundation_date'] ?? $legal->foundation_date) : $legal->foundation_date;
                $legal->legal_type_id = $legal->legal_type_id == null ? ($data['legal_type_id'] ?? $legal->legal_type_id) : $legal->legal_type_id;
                $legal->address_id = $legal->address_id == null ? ($data['business_address_id'] ?? $legal->address_id) : $legal->address_id;
                $legal->save();

                $person->display_name = $person->display_name == null ? ($legal->first_name ? ($legal->first_name . ' ' . $legal->last_name) : $person->display_name) : $person->display_name;
                $person->national_code = $person->national_code == null ? $data->nationalCode : $person->national_code;
                $person->profile_picture_id = $person->profile_picture_id == null ? ($data['avatar'] ?? null) : $person->profile_picture_id;
                $person->phone = $person->phone == null ? $data['phone'] : $person->phone;
                $person->signature_file_id = $person->signature_file_id == null ? $data['signatureFile'] : $person->signature_file_id;
                $person->save();
            }
        } else {
            if ($data->personType == 1) {
                if (isset($data->mobile)) {
                    $natural = Natural::where('mobile', '=', $data->mobile)->first();

                    if ($natural) {
                        return ['type' => 'mobile'];
                    }
                }
                $natural = new Natural();
                $natural->first_name = $data->first_name ?? null;
                $natural->last_name = $data->last_name ?? null;
                $natural->mobile = $data->mobile ?? null;
                $natural->phone_number = $data->phone_number ?? null;
                $natural->father_name = $data->father_name ?? null;
                $natural->birth_date = isset($data->birth_date) ? convertPersianToGregorianBothHaveTimeAndDont($data->birth_date) : null;
                $natural->bc_code = $data->bc_code ?? null;
                $natural->job = $data->job ?? null;
                $natural->isMarried = $data->is_married ?? null;
                $natural->level_of_spouse_education = $data->level_of_spouse_education ?? null;
                $natural->spouse_first_name = $data->spouse_first_name ?? null;
                $natural->spouse_last_name = $data->spouse_last_name ?? null;
                $natural->home_address_id = $data->home_address_id ?? null;
                $natural->job_address_id = $data->job_address_id ?? null;
                $natural->gender_id = $data->gender_id ?? 1;
                $natural->bc_issue_date = isset($data->bc_issue_date) ? convertPersianToGregorianBothHaveTimeAndDont($data->bc_issue_date) : null;
                $natural->bc_issue_location = $data->bc_issue_location ?? null;
                $natural->birth_location = $data->birth_location ?? null;
                $natural->bc_serial = $data->bc_serial ?? null;
                $natural->religion_id = $data->religion_id ?? null;
                $natural->religion_type_id = $data->religion_type_id ?? null;
                $natural->save();

                $person = new Person();
                $person->display_name = $natural->first_name ? ($natural->first_name . ' ' . $natural->last_name) : $person->display_name;
                $person->national_code = $data->nationalCode;
                $person->profile_picture_id = $data->avatar ?? null;
                $person->personable_type = Natural::class;
                $person->personable_id = $natural->id;
                $person->phone = $data->phone ?? null;
                $person->signature_file_id = $data->signatureFile ?? null;

                $person->save();
                if ($natural->gender_id == 1) {
                    $militaryService = MilitaryService::where('person_id', $person->id)->first();
                    if (!$militaryService) {
                        MilitaryService::create([
                            'person_id' => $person->id,
                            'exemption_type_id' => $data->exemptionTypeID ?? null,
                            'military_service_status_id' => $data->militaryServiceStatusID,
                            'work_force_id' => null,
                            'issue_date' => isset($data->issueDate) ? convertPersianToGregorianBothHaveTimeAndDont($data->issueDate) : now(),
                        ]);
                    }
                }

                $status = $this->activePersonStatus();

                $natural->person->status()->attach($status->id);
            } else {
                $legal = new Legal();
                $legal->name = $data['name'];
                $legal->registration_number = $data['registration_number'] ?? null;
                $legal->foundation_date = $data['foundation_date'] ?? null;
                $legal->legal_type_id = $data['legal_type_id'] ?? null;
                $legal->address_id = $data['business_address_id'] ?? null;
                $legal->save();

                $person = new Person();
                $person->display_name = $legal->name;
                $person->national_code = $data['national_code'];
                $person->profile_picture_id = $data['avatar'] ?? null;
                $person->phone = $data['phone'] ?? null;
                $person->signature_file_id = $data['signatureFile'] ?? null;

                $legal->person()->save($person);
                $status = $this->activePersonStatus();
                $legal->person->status()->attach($status->id);

            }
        }

        return $person;
    }

    public
    function insertLicenses($personId, $data)
    {
        $nationalLicense = PersonLicense::where('license_type', PersonLicensesEnums::NATIONAL_ID_CARD->value)->where('person_id', $personId)->first();
        $status = $this->personLicenseApprovedStatus()->id;
        if (!$nationalLicense) {
            $license = new PersonLicense();
            $license->file_id = $data->national_card_file_id ?? null;
            $license->person_id = $personId;
            $license->license_type = PersonLicensesEnums::NATIONAL_ID_CARD->value;
            $license->status_id = $status;
            $license->save();
        }


        $birthLicense = PersonLicense::where('license_type', PersonLicensesEnums::BIRTH_CERTIFICATE->value)->where('person_id', $personId)->first();
        if (!$birthLicense) {
            $license = new PersonLicense();
            $license->file_id = $data->birth_certificate_file_id ?? null;
            $license->person_id = $personId;
            $license->license_type = PersonLicensesEnums::BIRTH_CERTIFICATE->value;
            $license->status_id = $status;
            $license->save();
        }

    }

    public function bulkStorePersonLicenses(array $data, int $personID, Status $status = null)
    {
        if (is_null($status)) {
            $status = $this->personLicensePendingStatus();
        }
        $data = $this->personLicenseDataPreparation($data, $personID, $status);
        $result = PersonLicense::upsert($data->toArray(), ['id']);

    }

    public function caseCreatePersonStatus()
    {
        return Person::GetAllStatuses()->firstWhere('name', '=', PersonStatusEnum::CASE_CREATED->value);
    }

    public function pendingToFillPersonStatus()
    {
        return Person::GetAllStatuses()->firstWhere('name', '=', PersonStatusEnum::PENDING_TO_FILL->value);
    }

    public function pendingToApprovePersonStatus()
    {
        return Person::GetAllStatuses()->firstWhere('name', '=', PersonStatusEnum::PENDING_TO_APPROVE->value);
    }

    public function confirmedPersonStatus()
    {
        return Person::GetAllStatuses()->firstWhere('name', '=', PersonStatusEnum::CONFIRMED->value);
    }

    public function updatedPersonStatus()
    {
        return Person::GetAllStatuses()->firstWhere('name', '=', PersonStatusEnum::UPDATED->value);
    }

    public function personLicensePendingStatus()
    {
        return PersonLicense::GetAllStatuses()->firstWhere('name', '=', PersonLicenseStatusEnum::PENDING->value);
    }

    public function personLicenseApprovedStatus()
    {
        return PersonLicense::GetAllStatuses()->firstWhere('name', '=', PersonLicenseStatusEnum::APPROVED->value);
    }

    public function calculatePersonStatus(int $personID)
    {
        $person = Person::with([
            'natural.spouse.latestStatus', 'avatar', 'latestStatus'])
            ->joinRelationship('workForce.employee')
            ->finalPersonStatus()
            ->addSelect([
                'ps.name as status_name',
                'ps.class_name as status_class_name',
                'employees.personnel_code',
            ])
            ->find($personID);
        $pls = PersonLicense::where('person_id', $person->id)
            ->whereIntegerInRaw('license_type', [
                PersonLicensesEnums::BIRTH_CERTIFICATE->value,
                PersonLicensesEnums::MARRIAGE_PAGE->value,
                PersonLicensesEnums::CHILDREN_PAGE->value,
                PersonLicensesEnums::NATIONAL_ID_CARD->value,
                PersonLicensesEnums::BACK_OF_ID_CARD->value,
            ])
            ->joinRelationship('status')
            ->addSelect([
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
            ])
            ->get();
        $confirmedPLs = $pls->where('status_name', PersonLicenseStatusEnum::APPROVED->value);
        $pendingPLs = $pls->where('status_name', PersonLicenseStatusEnum::PENDING->value);

        $childrenStatus = Dependent::where('main_person_id', $personID)->joinRelationship('status', function ($join) {
            $join->where('name', DependentStatusEnum::PENDING->value);
        })->exists();

        $isarStatus = Isar::where('person_id', $personID)->joinRelationship('status')
            ->addSelect([
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
            ])
            ->first();

        $educationStatus = EducationalRecord::where('person_id', $personID)->joinRelationship('status')
            ->addSelect([
                'statuses.name as status_name',
                'statuses.class_name as status_class_name',
            ])
            ->get();

        $personalInfoStatusObject = $pls->isEmpty() ? [
            'name' => PersonStatusEnum::PENDING_TO_FILL->value,
            'className' => PersonStatusEnum::PENDING_TO_FILL->getClassName(),
        ] : ($confirmedPLs->isEmpty() && $pendingPLs->isNotEmpty() ? [
            'name' => PersonStatusEnum::PENDING_TO_APPROVE->value,
            'className' => PersonStatusEnum::PENDING_TO_APPROVE->getClassName(),
        ] : [
            'name' => PersonStatusEnum::CONFIRMED->value,
            'className' => PersonStatusEnum::CONFIRMED->getClassName(),
        ]);

        $spouseInfoStatusObject = is_null($person->natural->isMarried) && is_null($person->natural->spouse) ? [
            'name' => PersonStatusEnum::PENDING_TO_FILL->value,
            'className' => PersonStatusEnum::PENDING_TO_FILL->getClassName(),

        ] : ($person->natural->isMarried != 1 && !is_null($person->natural->isMarried) ? $personalInfoStatusObject : [
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
            'name' => IsarStatusEnum::PENDING_TO_FILL->value,
            'className' => 'warning'
        ] : [
            'name' => $isarStatus->status_name,
            'className' => $isarStatus->status_class_name
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
                'personnelCode' => $person->personnel_code,
                'status' => [
                    'name' => $person->status_name,
                    'className' => $person->status_class_name,
                ],
            ],
            'statuses' => [
                'personalData' => $personalInfoStatusObject,
                'spouseData' => $spouseInfoStatusObject,
                'childrenData' => $childrenInfoStatusObject,
                'isarData' => $isarInfoStatusObject,
                'educationData' => $educationInfoStatusObject,
            ],
        ];

        return $result;
    }

    public function confirmPerson(Person $person)
    {
        $calcStatus = $this->calculatePersonStatus($person->id);
        $statuses = array_column($calcStatus['statuses'], 'name');
        $uniqueValues = array_unique($statuses);

        if (count($uniqueValues) === 1 && reset($uniqueValues) === PersonStatusEnum::CONFIRMED->value) {
            $person->statuses()->attach($this->confirmedPersonStatus()->id);
        }
    }

    public function personLicenseDataPreparation(array $data, int $personID, ?Status $status)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }


        $data = collect($data)->map(function ($item) use ($personID, $status) {
            return [
                'id' => $item['id'] ?? null,
                'file_id' => $item['fileID'] ?? null,
                'person_id' => $personID,
                'license_type' => $item['licenseTypeID'],
                'status_id' => $status?->id,
            ];
        });
        return $data;
    }


}
