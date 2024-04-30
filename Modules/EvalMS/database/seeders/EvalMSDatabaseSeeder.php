<?php

namespace Modules\EvalMS\database\seeders;

use Illuminate\Database\Seeder;

class EvalMSDatabaseSeeder extends Seeder
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
         ]);
    }
}
