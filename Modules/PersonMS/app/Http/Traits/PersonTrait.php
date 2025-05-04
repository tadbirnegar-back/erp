<?php

namespace Modules\PersonMS\app\Http\Traits;

use Modules\HRMS\app\Models\ExemptionType;
use Modules\HRMS\app\Models\MilitaryService;
use Modules\PersonMS\app\Http\Enums\PersonLicensesEnums;
use Modules\PersonMS\app\Models\Legal;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;
use Modules\PersonMS\app\Models\PersonLicense;

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
            'gender_id' => $data['gender'] ?? 1,
            'bc_issue_date' => $data['bcIssueDate'] ?? null,
            'bc_issue_location' => $data['bcIssueLocation'] ?? null,
            'bc_serial' => $data['bcSerial'] ?? null,
            'religion_id' => $data['religionID'] ?? null,
            'religion_type_id' => $data['religionTypeID'] ?? null,


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

        $status = $this->activePersonStatus();

        $naturalPerson->person->status()->attach($status->id);


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

        $person = $naturalPerson->person;
        $person->display_name = $naturalPerson->first_name . ' ' . $naturalPerson->last_name;
        $person->national_code = $data['nationalCode'];
        $person->profile_picture_id = $data['avatar'] ?? null;

        $naturalPerson->person()->save($person);
        $statusID = $person->status;
        if (isset($data['statusID']) && $statusID[0]->id != $data['statusID']) {
            $naturalPerson->person->status()->attach($data['statusID']);
        }

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
                $natural->bc_issue_date = $natural->bc_issue_date == null ? ($data->bc_issue_date ?? null) : $natural->bc_issue_date;
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
                        $newMilitaryService = new MilitaryService();
                        $newMilitaryService->exemption_type_id = ($militaryService->exemptionType == null) ? ($data->exemptionTypeID ?? null) : $militaryService->exemptionType->id;
                        $newMilitaryService->military_service_status_id = ($militaryService->militaryServiceStatus == null) ? ($data->militaryServiceStatusID ?? null) : $militaryService->militaryServiceStatus->id;
                        $newMilitaryService->work_force_id = null;
                        $newMilitaryService->issue_date = ($militaryService->issueDate == null) ? (convertPersianToGregorianBothHaveTimeAndDont($data->issueDate) ?? null) : $militaryService->issueDate;
                        $newMilitaryService->person_id = $person->id;
                        $newMilitaryService->save();
                    } else {
                        $militaryService = new MilitaryService();
                        $militaryService->exemption_type_id = ($data->exemptionTypeID ?? null);
                        $militaryService->military_service_status_id = ($data->militaryServiceStatusID ?? null);
                        $militaryService->work_force_id = null;
                        $militaryService->issue_date = (convertPersianToGregorianBothHaveTimeAndDont($data->issueDate) ?? null);
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
                $natural->birth_date = convertPersianToGregorianBothHaveTimeAndDont($data->birth_date) ?? null;
                $natural->bc_code = $data->bc_code ?? null;
                $natural->job = $data->job ?? null;
                $natural->isMarried = $data->is_married ?? null;
                $natural->level_of_spouse_education = $data->level_of_spouse_education ?? null;
                $natural->spouse_first_name = $data->spouse_first_name ?? null;
                $natural->spouse_last_name = $data->spouse_last_name ?? null;
                $natural->home_address_id = $data->home_address_id ?? null;
                $natural->job_address_id = $data->job_address_id ?? null;
                $natural->gender_id = $data->gender_id ?? 1;
                $natural->bc_issue_date = $data->bc_issue_date ?? null;
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
                    MilitaryService::create([
                        'person_id' => $person->id,
                        'exemption_type_id' => $data->exemptionTypeID ?? null,
                        'military_service_status_id' => $data->militaryServiceStatusID,
                        'work_force_id' => null,
                        'issue_date' => convertPersianToGregorianBothHaveTimeAndDont($data->issueDate) ?? now(),
                    ]);
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
        $nationalLicense = PersonLicense::where('license_type', PersonLicensesEnums::NATIONAL_ID_CARD->id())->where('person_id', $personId)->first();
        if (!$nationalLicense) {
            $license = new PersonLicense();
            $license->file_id = $data->national_card_file_id ?? null;
            $license->person_id = $personId;
            $license->license_type = PersonLicensesEnums::NATIONAL_ID_CARD->id();
            $license->save();
        }


        $birthLicense = PersonLicense::where('page_number', 1)->where('license_type', PersonLicensesEnums::BIRTH_CERTIFICATE->id())->where('person_id', $personId)->first();
        if (!$birthLicense) {
            $license = new PersonLicense();
            $license->file_id = $data->birth_certificate_file_id ?? null;
            $license->person_id = $personId;
            $license->license_type = PersonLicensesEnums::BIRTH_CERTIFICATE->id();
            $license->save();
        }

    }


}
