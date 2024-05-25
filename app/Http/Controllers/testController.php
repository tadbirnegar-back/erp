<?php

namespace App\Http\Controllers;


use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Http\Repositories\RecruitmentScriptRepository;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\TownOfc;
use Modules\OUnitMS\app\Models\VillageOfc;
use Morilog\Jalali\Jalalian;

class testController extends Controller
{
    public function run()

    {


//        $user = User::find(1889);
//        $workForce = $user->person->workForce;
////        $user->load('organizationUnits.statuses');
//        $employee = $workForce->workForceable;
//        /**
//         * @var Employee $employee
//         */
//        $a = $employee->load('recruitmentScripts.status');
//        dd($a);
//        $organizationUnits = $user->organizationUnits()->join('village_ofcs', 'organization_units.unitable_id', '=', 'village_ofcs.id')
////            ->where('organization_units.head_id', 1905)
//            ->whereNotNull('organization_units.head_id')
//            ->where('organization_units.unitable_type', VillageOfc::class)
//            ->whereDoesntHave('payments')
//            ->whereNotNull('village_ofcs.degree')
//            ->exists();
//
//        dd($organizationUnits);
//        $a = Jalalian::fromFormat('Y/m/d', '1397/01/18');
//        dd($a->toCarbon()->timestamp);

//        $user = User::find(1906);
//        $user->roles()->sync([2,
//        ]);
//        \DB::transaction(function () {
//            $state = new StateOfc();
//            $state->save();
//            $ounit = new OrganizationUnit([
//                'name' => 'آذربایجان شرقی',
//                'head_id' => null,
//
//            ]);
//            $state->organizationUnit()->save($ounit);
//            $cityOfc = new CityOfc();
//            $cityOfc->state_ofc_id = $state->id;
//            $cityOfc->save();
//
//            $cityOunit = new OrganizationUnit([
//                'name' => 'مرند',
//                'head_id' => null,
//
//            ]);
//            $cityOfc->organizationUnit()->save($cityOunit);
//            $districtOfc = new DistrictOfc();
//            $districtOfc->city_ofc_id = $cityOfc->id;
//            $districtOfc->save();
//
//            $districtOunit = new OrganizationUnit([
//                'name' => 'مرکزی',
//                'head_id' => null,
//
//            ]);
//            $districtOfc->organizationUnit()->save($districtOunit);
//            $townOfc = new TownOfc();
//            $townOfc->district_ofc_id = $districtOfc->id;
//            $townOfc->save();
//
//            $townOunit = new OrganizationUnit([
//                'name' => 'پیام',
//                'head_id' => null,
//
//            ]);
//
//            $townOfc->organizationUnit()->save($townOunit);
//            $villageOfc = new VillageOfc();
//            $villageOfc->town_ofc_id = $townOfc->id;
//            $villageOfc->save();
//
//            $villageOunit = new OrganizationUnit([
//                'name' => 'یامچی',
//                'head_id' => null,
//            ]);
//
//            $villageOfc->organizationUnit()->save($villageOunit);
//        });
    }
}
