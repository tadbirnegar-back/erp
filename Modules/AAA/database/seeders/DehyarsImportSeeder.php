<?php

namespace Modules\AAA\database\seeders;

use Illuminate\Database\Seeder;
use Modules\AAA\app\Http\Repositories\UserRepository;
use Modules\AAA\app\Models\Role;
use Modules\HRMS\app\Http\Repositories\EmployeeRepository;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\RecruitmentScript;
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
        $villagers = json_decode(file_get_contents(realpath(__DIR__.'/dehyars.json')), true);

//        try {
            \DB::beginTransaction();
        foreach ($villagers as $villager) {

                if ($villager['nationalCode'] !== '') {
                    $villager['mobile']=ltrim($villager['mobile'], "0");
                    $personService = new PersonRepository();
                    $personResult = $personService->naturalExists($villager['nationalCode']);

                    if (is_null($personResult)) {
                        $villager['gender']=$villager['gender']=='مرد'?1:2;
                       $personResult= $personService->naturalStore($villager);
                        $villager['personID'] = $personResult->person->id;

                    }else{
                        $villager['personID'] = $personResult->id;

                    }

                    $villager['password'] = $villager['nationalCode'];
                    $userService = new UserRepository();
                    $userResult = $userService->isPersonUser($villager['personID']);
                    if (is_null($userResult)) {
                        $userResult = $userService->store($villager);
                    }

                    $studentRoles = Role::where('name', '=', 'فراگیر')->first('id');

                    $userResult->roles()->sync($studentRoles);
                    $villager['userID'] = $userResult->id;
                    /**
                     * @var Employee $employee
                     */
                    $employeeService = new EmployeeRepository();
                    $employee = $employeeService->store($villager);
                    $city = OrganizationUnit::with('unitable')->where('name', $villager['city'])->where('unitable_type',CityOfc::class)->first();


                    $dcName = $villager['district'];

                    $district=DistrictOfc::whereHas('organizationUnit',function ($query) use ($dcName) {
                        $query->where('name', $dcName);
                    })->where('city_ofc_id',$city->unitable->id)->first();

                    $townName = $villager['town'];

                    $town=TownOfc::whereHas('organizationUnit',function ($query) use ($townName) {
                        $query->where('name', $townName);
                    })->where('district_ofc_id',$district->id)->first();


                    $villName = $villager['village'];

                    $village=VillageOfc::whereHas('organizationUnit',function ($query) use ($villName){
                        $query->where('name', $villName);
                    })->where('town_ofc_id',$town->id)->first();

                    $vilageOU = $village->organizationUnit;

                    $vilageOU->head_id = $userResult->id;
                    $vilageOU->save();

                    $status = RecruitmentScript::GetAllStatuses()->where('name', '=', 'فعال')->first();

                    $rs = new RecruitmentScript();
                    $rs->organization_unit_id = $village->organizationUnit->id;
                    $rs->employee_id = $employee->id;
                    $rs->level_id = 1;
                    $rs->position_id = 1;
                    $rs->create_date = $villager['rsDate'];
                    $rs->save();

                    $rs->status()->attach($status->id);


                    $studentService = new StudentRepository();
                    $customerResult = $studentService->isPersonStudent($villager['personID']) ?? $studentService->store($villager);

                }



//        }
            \DB::commit();
//        }catch (\Exception $e){
            \DB::rollBack();

        }
        // $this->call([]);
    }
}
