<?php

namespace Modules\OUnitMS\database\seeders;

use Illuminate\Database\Seeder;

class OUnitMSDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
//             OrganizationUnitStatusSeeder::class,
//             LocationsSeeder::class,
//             OrganizationParentSeeder::class,
//             VillageOfcDegreeSeeder::class,
//            OlderNamesOfVillageSeeder::class,
//            PermissionSeeder::class
            OUinitStatusSeeder::class
        ]);
    }
}
