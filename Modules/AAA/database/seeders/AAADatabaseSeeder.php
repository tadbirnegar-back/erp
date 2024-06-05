<?php

namespace Modules\AAA\database\seeders;

use Illuminate\Database\Seeder;

class AAADatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
//            PermissionTypeSeeder::class,
//            ModuleCategorySeeder::class,
//            ModuleSeeder::class,
//            PermissionSeeder::class,
//            UserStatusSeeder::class,

            DehyarsImportSeeder::class,
            // PrefectsSeeder::class,

        ]);
    }
}
