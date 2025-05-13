<?php

namespace Modules\SMM\database\seeders;

use Illuminate\Database\Seeder;

class SMMDatabaseSeeder extends Seeder
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
            CircularStatusSeeder::class,
        ]);
    }
}
