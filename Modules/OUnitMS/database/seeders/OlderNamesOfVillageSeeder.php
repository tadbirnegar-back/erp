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


                $city = OrganizationUnit::where('name', $villager['city'])->where('unitable_type', CityOfc::class)->first();


                $dcName = $villager['district'];

//                $district = DistrictOfc::whereHas('organizationUnit', function ($query) use ($dcName) {
//                    $query->where('name', $dcName);
//                })->where('city_ofc_id', $city->unitable->id)->first();

                $district = OrganizationUnit::where('name', $dcName)
                    ->where('unitable_type', DistrictOfc::class)
                    ->where('parent_id', $city->id)
                    ->first();

                $townName = $villager['town'];

//                $town = TownOfc::whereHas('organizationUnit', function ($query) use ($townName) {
//                    $query->where('name', $townName);
//                })->where('district_ofc_id', $district->id)->first();

                $town = OrganizationUnit::where('name', $townName)
                    ->where('unitable_type', TownOfc::class)
                    ->where('parent_id', $district->id)->first();

                $villName = $villager['village'];

//                $village = VillageOfc::whereHas('organizationUnit', function ($query) use ($villName) {
//                    $query->where('name', $villName);
//                })->where('town_ofc_id', $town->id)->first();

                $village = OrganizationUnit::where('name', $villName)->where('unitable_type', VillageOfc::class)->where('parent_id', $town->id)->first();

                if (is_null($village)) {
                    dd($villName, $town, $district, $city);
                }
                $village = $village->unitable;
//                if (isset($villager['degree'])) {
                $village->hierarchy_code = $villager['hierarchy_code'] ?? null;
                $village->national_uid = $villager['national_uid'] ?? null;
                $village->abadi_code = $villager['abadi_code'] ?? null;
                $village->isTourism = $villager['isTourism'] ?? 0;
                $village->isFarm = $villager['isFarm'] ?? 0;
                $village->isAttached_to_city = $villager['isAttached_to_city'] ?? 0;
                $village->hasLicense = $villager['hasLicense'] ?? 0;
                $village->license_number = $villager['license_number'] ?? null;
                $village->save();
//                }

            }
        });
    }
}
