<?php

namespace Modules\OUnitMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
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
            $records[$village[0]][$village[1]][] = $village[3];
            return $records;
        }, []);

        $state = new StateOfc();
        $state->save();
        $ounit = new OrganizationUnit([
            'name' => 'آذربایجان غربی',
            'head_id' => null,

        ]);
//        $ounit->name = 'آذربایجان غربی';
//        $ounit->head_id = null;
        $state->organizationUnit()->save($ounit);


        foreach ($records as $city => $districts) {

            $cityOfc = new CityOfc();
            $cityOfc->state_ofc_id = $state->id;
            $cityOfc->save();

            $cityOunit =new OrganizationUnit([
                'name' => $city,
                'head_id' => null,

            ]);
//            $cityOunit->name = $city;
//            $cityOunit->head_id = null;

            $cityOfc->organizationUnit()->save($cityOunit);

            foreach ($districts as $district => $villages) {
                $districtOfc = new DistrictOfc();
                $districtOfc->city_ofc_id = $cityOfc->id;
                $districtOfc->save();

                $districtOunit =new OrganizationUnit([
                    'name' => $district,
                    'head_id' => null,

                ]);
//                $districtOunit->name = $district;
//                $districtOunit->head_id = null;

                $districtOfc->organizationUnit()->save($districtOunit);


                foreach ($villages as $village) {
                    $villageOfc = new VillageOfc();
                    $villageOfc->district_ofc_id = $districtOfc->id;
                    $villageOfc->save();

                    $villageOunit =new OrganizationUnit([
                        'name' => $village,
                        'head_id' => null,

                    ]);
//                    $villageOunit->name = $village;
//                    $villageOunit->head_id = null;

                    $villageOfc->organizationUnit()->save($villageOunit);
                }

            }



        }
        // $this->call([]);
    }
}
