<?php

namespace Modules\CustomerMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\CustomerMS\database\seeders\ModuleCategorySeeder;

class CustomerMSDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ModuleCategorySeeder::class,
            ModuleSeeder::class,
            PermissionSeeder::class,
            CustomerStatusSeeder::class,

        ]);
        // $this->call([]);
    }
}
