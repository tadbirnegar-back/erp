<?php

namespace Modules\AddressMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\AddressMS\app\Models\City;
use Modules\AddressMS\app\Models\District;
use Modules\AddressMS\app\Models\State;
use Modules\AddressMS\app\Models\Town;
use Modules\AddressMS\app\Models\Village;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\TownOfc;
use Modules\OUnitMS\app\Models\VillageOfc;

class LocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $villages = json_decode(file_get_contents(realpath(__DIR__ . '/locations.json')), true);
        $records = array_reduce($villages, function ($records, $village) {
            $records[$village[0]][$village[1]][$village[2]][] = $village[3];
            return $records;
        }, []);

//        $state = new StateOfc();
//        $state->save();
        $state = new State([
            'name' => 'آذربایجان غربی',
            'country_id' => 1

        ]);
//        $ounit->name = 'آذربایجان غربی';
//        $ounit->head_id = null;
        $state->save();


        foreach ($records as $city => $districts) {

            $cityOfc = new City();
            $cityOfc->name = $city;
            $cityOfc->state_id = $state->id;
            $cityOfc->save();

//            $cityOunit = new OrganizationUnit([
//                'name' => $city,
//                'head_id' => null,
//
//            ]);
//            $cityOunit->name = $city;
//            $cityOunit->head_id = null;

//            $cityOfc->save();

            foreach ($districts as $district => $towns) {
                $districtOfc = new District();
                $districtOfc->name = $district;
                $districtOfc->city_id = $cityOfc->id;
                $districtOfc->save();

//                $districtOunit = new OrganizationUnit([
//                    'name' => $district,
//                    'head_id' => null,
//
//                ]);
//                $districtOunit->name = $district;
//                $districtOunit->head_id = null;

//                $districtOfc->organizationUnit()->save($districtOunit);

                foreach ($towns as $town => $villages) {

                    $townOfc = new Town();
                    $townOfc->name = $town;
                    $townOfc->district_id = $districtOfc->id;
                    $townOfc->save();

//                    $townOunit = new OrganizationUnit([
//                        'name' => $town,
//                        'head_id' => null,
//
//                    ]);
//
//                    $townOfc->organizationUnit()->save($townOunit);

                    foreach ($villages as $village) {
                        $villageOfc = new Village();
                        $villageOfc->name = $village;
                        $villageOfc->town_id = $townOfc->id;
                        $villageOfc->save();

//                        $villageOunit = new OrganizationUnit([
//                            'name' => $village,
//                            'head_id' => null,
//
//                        ]);
//                    $villageOunit->name = $village;
//                    $villageOunit->head_id = null;

//                        $villageOfc->organizationUnit()->save($villageOunit);
                    }
                }


            }


        }
        // $this->call([]);
    }
}
