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
use Modules\WidgetsMS\app\Models\Widget;
use Morilog\Jalali\Jalalian;

class testController extends Controller
{
    public function run()
    {
        $o = OrganizationUnit::where('unitable_type', DistrictOfc::class)->get('id');

        dd($o->pluck('id')->toArray());

//        $a='[
//    {
//        "city": "ارومیه",
//        "ردیف": 1,
//        "district": "انزل",
//        "mobile": 9128243649,
//        "username": "urm-anz"
//    },
//    {
//        "city": "ارومیه",
//        "ردیف": 2,
//        "district": "سیلوانه",
//        "mobile": 9141468259,
//        "username": "urm-sil"
//    },
//    {
//        "city": "ارومیه",
//        "ردیف": 3,
//        "district": "صومای برادوست",
//        "mobile": 9147126163,
//        "username": "urm-sum"
//    },
//    {
//        "city": "ارومیه",
//        "ردیف": 4,
//        "district": "مرکزی",
//        "mobile": 9143895465,
//        "username": "urm-mar"
//    },
//    {
//        "city": "ارومیه",
//        "ردیف": 5,
//        "district": "نازلو",
//        "mobile": 9144482991,
//        "username": "urm-naz"
//    },
//    {
//        "city": "اشنویه",
//        "ردیف": 6,
//        "district": "مرکزی",
//        "mobile": 9144440798,
//        "username": "osh-mar"
//    },
//    {
//        "city": "اشنویه",
//        "ردیف": 7,
//        "district": "نالوس",
//        "mobile": 9144424739,
//        "username": "osh-nal"
//    },
//    {
//        "city": "باروق",
//        "ردیف": 8,
//        "district": "مرکزی",
//        "mobile": 9141814565,
//        "username": "bar-mar"
//    },
//    {
//        "city": "باروق",
//        "ردیف": 9,
//        "district": "نختالو",
//        "mobile": 9149805823,
//        "username": "bar-nok"
//    },
//    {
//        "city": "بوکان",
//        "ردیف": 10,
//        "district": "سیمینه",
//        "mobile": 9141805992,
//        "username": "buk-sim"
//    },
//    {
//        "city": "بوکان",
//        "ردیف": 11,
//        "district": "مرکزی",
//        "mobile": 9143800401,
//        "username": "buk-mar"
//    },
//    {
//        "city": "پلدشت",
//        "ردیف": 12,
//        "district": "ارس",
//        "mobile": 9147255330,
//        "username": "pol-ara"
//    },
//    {
//        "city": "پلدشت",
//        "ردیف": 13,
//        "district": "مرکزی",
//        "mobile": 9149633041,
//        "username": "pol-mar"
//    },
//    {
//        "city": "پیرانشهر",
//        "ردیف": 14,
//        "district": "لاجان",
//        "mobile": 9144444843,
//        "username": "pir-laj"
//    },
//    {
//        "city": "پیرانشهر",
//        "ردیف": 15,
//        "district": "مرکزی",
//        "mobile": 9126700834,
//        "username": "pir-mar"
//    },
//    {
//        "city": "تکاب",
//        "ردیف": 16,
//        "district": "تخت سلیمان",
//        "mobile": 9193416311,
//        "username": "tek-tak"
//    },
//    {
//        "city": "تکاب",
//        "ردیف": 17,
//        "district": "مرکزی",
//        "mobile": 9147259816,
//        "username": "tek-mar"
//    },
//    {
//        "city": "چالدران",
//        "ردیف": 18,
//        "district": "دشتک",
//        "mobile": 9904255012,
//        "username": "cha-das"
//    },
//    {
//        "city": "چالدران",
//        "ردیف": 19,
//        "district": "مرکزی",
//        "mobile": 9143639318,
//        "username": "cha-mar"
//    },
//    {
//        "city": "چایپاره",
//        "ردیف": 20,
//        "district": "حاجیلار",
//        "mobile": 9149623357,
//        "username": "cha-haj"
//    },
//    {
//        "city": "چایپاره",
//        "ردیف": 21,
//        "district": "مرکزی",
//        "mobile": 9149773997,
//        "username": "cha-mar2"
//    },
//    {
//        "city": "چهاربرج",
//        "ردیف": 22,
//        "district": "فیروزآباد",
//        "mobile": 9141808295,
//        "username": "cha-fir"
//    },
//    {
//        "city": "چهاربرج",
//        "ردیف": 23,
//        "district": "مرکزی",
//        "mobile": 9149819067,
//        "username": "cha-mar3"
//    },
//    {
//        "city": "خوی",
//        "ردیف": 24,
//        "district": "ایواوغلی",
//        "mobile": 9143635202,
//        "username": "kho-evo"
//    },
//    {
//        "city": "خوی",
//        "ردیف": 25,
//        "district": "صفاییه",
//        "mobile": 9141651394,
//        "username": "kho-saf"
//    },
//    {
//        "city": "خوی",
//        "ردیف": 26,
//        "district": "فیرورق",
//        "mobile": 9143465375,
//        "username": "kho-fir"
//    },
//    {
//        "city": "خوی",
//        "ردیف": 27,
//        "district": "قطور",
//        "mobile": 9144616519,
//        "username": "kho-gho"
//    },
//    {
//        "city": "خوی",
//        "ردیف": 28,
//        "district": "مرکزی",
//        "mobile": 9212740589,
//        "username": "kho-mar"
//    },
//    {
//        "city": "سردشت",
//        "ردیف": 29,
//        "district": "ربط",
//        "mobile": 9149580889,
//        "username": "sar-rab"
//    },
//    {
//        "city": "سردشت",
//        "ردیف": 30,
//        "district": "مرکزی",
//        "mobile": 9143443705,
//        "username": "sar-mar"
//    },
//    {
//        "city": "سلماس",
//        "ردیف": 31,
//        "district": "کوهسار",
//        "mobile": 9143456745,
//        "username": "sal-kuh"
//    },
//    {
//        "city": "سلماس",
//        "ردیف": 32,
//        "district": "مرکزی",
//        "mobile": 9143401006,
//        "username": "sal-mar"
//    },
//    {
//        "city": "شاهین دژ",
//        "ردیف": 33,
//        "district": "کشاورز",
//        "mobile": 9143480140,
//        "username": "sha-kes"
//    },
//    {
//        "city": "شاهین دژ",
//        "ردیف": 34,
//        "district": "مرکزی",
//        "mobile": 9144822624,
//        "username": "sha-mar"
//    },
//    {
//        "city": "شوط",
//        "ردیف": 35,
//        "district": "قره قویون",
//        "mobile": 9141654258,
//        "username": "sho-gha"
//    },
//    {
//        "city": "شوط",
//        "ردیف": 36,
//        "district": "مرکزی",
//        "mobile": 9144621548,
//        "username": "sho-mar"
//    },
//    {
//        "city": "ماکو",
//        "ردیف": 37,
//        "district": "بازرگان",
//        "mobile": 9141646735,
//        "username": "mak-baz"
//    },
//    {
//        "city": "ماکو",
//        "ردیف": 38,
//        "district": "مرکزی",
//        "mobile": 9141606840,
//        "username": "mak-mar"
//    },
//    {
//        "city": "مهاباد",
//        "ردیف": 39,
//        "district": "خلیفان",
//        "mobile": 9188744307,
//        "username": "mah-kha"
//    },
//    {
//        "city": "مهاباد",
//        "ردیف": 40,
//        "district": "مرکزی",
//        "mobile": 9116628197,
//        "username": "mah-mar"
//    },
//    {
//        "city": "میاندوآب",
//        "ردیف": 41,
//        "district": "بکتاش",
//        "mobile": 9141804296,
//        "username": "mia-bak"
//    },
//    {
//        "city": "میاندوآب",
//        "ردیف": 42,
//        "district": "مرکزی",
//        "mobile": 9143810781,
//        "username": "mia-mar"
//    },
//    {
//        "city": "میرآباد",
//        "ردیف": 43,
//        "district": "زاب",
//        "mobile": 9143400593,
//        "username": "mir-zab"
//    },
//    {
//        "city": "میرآباد",
//        "ردیف": 44,
//        "district": "مرکزی",
//        "mobile": 9120153396,
//        "username": "mir-mar"
//    },
//    {
//        "city": "نقده",
//        "ردیف": 45,
//        "district": "محمدیار",
//        "mobile": 9143895911,
//        "username": "nag-moh"
//    },
//    {
//        "city": "نقده",
//        "ردیف": 46,
//        "district": "مرکزی",
//        "mobile": 9144405323,
//        "username": "nag-mar"
//    }
//]
//';
//        $b = json_decode($a, true);
//        $b = collect($b);
//        $c = $b->pluck('mobile');
////        dd($c);
////        foreach ($b as $item) {
//            $user = User::whereNotIn('mobile',$c->toArray())->get();
//
//        foreach ($user as $item) {
//          $r=  $item->activeWidgets()->where('permission_id', 122)->get();
//            if (is_null($r)) {
//                $w = new Widget();
//                $w->user_id = $item->id;
//                $w->permission_id = 122;
//                $w->isActivated = 1;
//                $w->save();
//            }
//
//            }

//            if (is_null($user)) {
//                dd($item);
//            }
//            $user->password=bcrypt(str($item['mobile']));
//            $user->save();
//        }
//            $item['personID'] = $user->person->id;
//
//            $employee = $user->person->workForce->workforceable;
//
//            $city = OrganizationUnit::where('name', $item['city'])->where('unitable_type', CityOfc::class)->first();
//
//            if (is_null($city)) {
//                dd($item['city']);
//            }
//            $dcName = $item['district'];
//
//            $district = OrganizationUnit::where('name',$dcName)->where('unitable_type', DistrictOfc::class)
//                ->where('parent_id', $city->id)->first();
//            $districtOU = $district;
//            $status = RecruitmentScript::GetAllStatuses()->where('name', '=', 'فعال')->first();
//            $rs = new RecruitmentScript();
//            $rs->organization_unit_id = $districtOU->id;
//            $rs->employee_id = $employee->id;
//            $rs->level_id = 1;
//            $rs->position_id = 2;
//            $rs->create_date = $item['rsDate'] ?? null;
//            $rs->save();
//            $rs->status()->attach($status->id);
//        }
//
//        dd('done');

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
