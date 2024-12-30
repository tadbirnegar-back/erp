<?php

namespace Modules\ACMS\database\seeders;

use Illuminate\Database\Seeder;

class ACMSDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ModuleCategorySeeder::class,
            ModuleSeeder::class,
            PermissionsSeeder::class,
            StatusSeeder::class,
        ]);
    }
}
