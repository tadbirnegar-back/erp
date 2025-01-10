<?php

namespace Modules\ACC\database\seeders;

use Illuminate\Database\Seeder;

class ACCDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            AccountCategoryTypeSeeder::class,
            AccountCategorySeeder::class,
            ModuleCategorySeeder::class,
            ModuleSeeder::class,
            PermissionsSeeder::class,
            StatusSeeder::class,
            DocumentStatusSeeder::class,
        ]);

    }
}
