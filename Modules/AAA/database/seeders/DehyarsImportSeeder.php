<?php

namespace Modules\AAA\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Http\Repositories\UserRepository;
use Modules\AAA\app\Models\Role;
use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Http\Repositories\EmployeeRepository;
use Modules\HRMS\app\Models\EducationalRecord;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\LevelOfEducation;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\Relative;
use Modules\LMS\app\Http\Repository\StudentRepository;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\TownOfc;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PersonMS\app\Http\Repositories\PersonRepository;

class DehyarsImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $villagers = json_decode(file_get_contents(realpath(__DIR__ . '/dehyars2.json')), true);

//        try {
//        \DB::beginTransaction();
        DB::transaction(function () use ($villagers) {

            foreach ($villagers as $villager) {
                if (isset($villager['nationalCode']) && $villager['nationalCode'] !== '') {
                    $villager['mobile'] = ltrim($villager['mobile'], "0");
                    $personService = new PersonRepository();
                    $personResult = $personService->naturalExists($villager['nationalCode']);

                    if (is_null($personResult)) {
                        $villager['gender'] = $villager['gender'] == 'مرد' ? 1 : 2;
                        $personResult = $personService->naturalStore($villager);
                        $villager['personID'] = $personResult->person->id;

                    } else {
                        $villager['personID'] = $personResult->id;

                    }

                    $villager['password'] = $villager['nationalCode'];
                    $userService = new UserRepository();
//                    if ($villager['mobile'] === "9143492432") {
//                        $userResult = $userService->isPersonUser($villager['personID']);
//                        $user = User::where('person_id', $villager['personID'])->first();
//                        dd($personResult, $userResult,$user);
////
////                dd($userResult);
//                    }
                    $userResult = $userService->isPersonUser($villager['personID']);
//                    $userResult = User::where('mobile', $villager['mobile'])->first();
//                    if ($villager['mobile'] === "9143492432" && is_null($userResult)) {
//                        dd($personResult,$userResult, $villager);
////
////                dd($userResult);
//                    }
                    if (is_null($userResult)) {
//                    dd(true);
                        $userResult = $userService->store($villager);
                    }
//                if ($villager['mobile']==="9149752701") {
////                    dd($personResult, $villager);
//
//                dd($userResult);
//                }
                    $studentRoles = Role::where('name', '=', 'فراگیر')->first('id');

                    $userResult->roles()->sync($studentRoles);
                    $villager['userID'] = $userResult->id;
                    /**
                     * @var Employee $employee
                     */
                    $employeeService = new EmployeeRepository();
                    $employee = $employeeService->isPersonEmployee($villager['personID']);




                    if (is_null($employee)) {
                        $employee = $employeeService->store($villager);
                    }

                    $educationalRecord = new EducationalRecord();
                    $edu = $villager['education'] ?? 'لیسانس';
                    $lvlOfEdu = LevelOfEducation::where('name', '%like%', $edu)->first();


                    $educationalRecord->field_of_study = $villager['major'] ?? 'تجربی';
                    $educationalRecord->work_force_id = $employee->workForce->id;
                    $educationalRecord->level_of_educational_id = $lvlOfEdu->id ?? null;

                    $educationalRecord->save();


                    $relative = new Relative();

                    $relative->full_name = $villager['fatherName'] ?? null . ' ' . $villager['lastName'];
                    $relative->work_force_id = $employee->workForce->id;

                    $relative->save();

                    $city = OrganizationUnit::with('unitable')->where('name', $villager['city'])->where('unitable_type', CityOfc::class)->first();


                    $dcName = $villager['district'];

                    $district = DistrictOfc::whereHas('organizationUnit', function ($query) use ($dcName) {
                        $query->where('name', $dcName);
                    })->where('city_ofc_id', $city->unitable->id)->first();

                    $townName = $villager['town'];

                    $town = TownOfc::whereHas('organizationUnit', function ($query) use ($townName) {
                        $query->where('name', $townName);
                    })->where('district_ofc_id', $district->id)->first();


                    $villName = $villager['village'];

                    $village = VillageOfc::whereHas('organizationUnit', function ($query) use ($villName) {
                        $query->where('name', $villName);
                    })->where('town_ofc_id', $town->id)->first();

                    if (is_null($village)) {
                        dd($villager);
                    }
                    $vilageOU = $village->organizationUnit;

                    $vilageOU->head_id = $userResult->id;
                    $vilageOU->save();

                    $status = RecruitmentScript::GetAllStatuses()->where('name', '=', 'فعال')->first();

                    $rs = new RecruitmentScript();
                    $rs->organization_unit_id = $village->organizationUnit->id;
                    $rs->employee_id = $employee->id;
                    $rs->level_id = 1;
                    $rs->position_id = 1;
                    $rs->create_date = $villager['rsDate'] ?? null;
                    $rs->save();

                    $rs->status()->attach($status->id);


                    $studentService = new StudentRepository();
                    $customerResult = $studentService->isPersonStudent($villager['personID']) ?? $studentService->store($villager);

                }


//        }

//        }catch (\Exception $e){


            }
        });

//        \DB::commit();
//        \DB::rollBack();
        // $this->call([]);
    }
}
