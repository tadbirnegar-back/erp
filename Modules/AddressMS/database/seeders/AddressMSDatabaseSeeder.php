<?php

namespace Modules\AddressMS\database\seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AddressMSDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $statesData = json_decode(file_get_contents(realpath(__DIR__.'/cities.json')), true);

        $country = DB::table('countries')->insertGetId([
            'name' => 'ایران',
        ]);
        foreach ($statesData as $stateData) {
            $stateId = DB::table('states')->insertGetId([
                'name' => $stateData['name'],
                'country_id' => $country,
            ]);

            foreach ($stateData['cities'] as $cityName) {
                DB::table('cities')->insert([
                    'name' => $cityName,
                    'state_id' => $stateId,
                ]);
            }
        }

        $this->call([
            ModuleCategorySeeder::class,
            ModuleSeeder::class,
            PermissionSeeder::class,
        ]);
    }
}
