<?php

namespace Modules\PersonMS\app\Http\Traits;

use Modules\PersonMS\app\Models\Legal;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;

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

    public function naturalStore(array $data): Natural
    {

        $naturalPerson = new Natural();
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
            'bc_serial' => $data['bcSerial'] ?? null,
            'religion_id' => $data['religionID'] ?? null,
            'religion_type_id' => $data['religionTypeID'] ?? null,


        ]);


        $naturalPerson->save();

        $person = new Person();
        $person->fill([
            'display_name' => $naturalPerson->first_name . ' ' . $naturalPerson->last_name,
            'national_code' => $data['nationalCode'],
            'profile_picture_id' => $data['avatar'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);

        $naturalPerson->person()->save($person);

        $status = $this->activePersonStatus();

        $naturalPerson->person->status()->attach($status->id);


        return $naturalPerson;


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
        $person->national_code = $data['national_code'] ?? null;
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

    public function personNaturalUpdate(array $data,Person $person)
    {
        $naturalPerson=$person->personable;

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
      return  Person::where('national_code',$nationalCode)->whereHas('latestRecruitmentScript', function ($query) {

            $query->where('expire_date', '>', now())
                ->whereDoesntHave('latestStatus',function ($query) {
                    $query->where('name','=','غیرفعال');
                });

        })->with(['latestRecruitmentScript.issueTime'])->first();
    }

    public function activePersonStatus()
    {
        return Person::GetAllStatuses()->firstWhere('name', '=', 'فعال');
    }
}
