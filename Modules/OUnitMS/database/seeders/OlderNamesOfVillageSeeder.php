<?php

namespace Modules\OUnitMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\TownOfc;
use Modules\OUnitMS\app\Models\VillageOfc;

class OlderNamesOfVillageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $villagers = json_decode(file_get_contents(realpath(__DIR__ . '/olderVillageNamesUpdate.json')), true);

        DB::transaction(function () use ($villagers) {
            foreach ($villagers as $villager) {


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
//                if (isset($villager['degree'])) {
                    $village->village_name_in_85 = $villager['villageNameIn85'] ?? null;
                    $village->village_name_in_90 = $villager['villageNameIn90'] ?? null;
                    $village->village_name_in_95 = $villager['villageNameIn95'] ?? null;
                    $village->save();
//                }

            }
        });
    }
}
