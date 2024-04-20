<?php

namespace Modules\BranchMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchMSDatabaseSeeder extends Seeder
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
            BranchStatusSeeder::class,

        ]);
        // $this->call([]);
    }
}
