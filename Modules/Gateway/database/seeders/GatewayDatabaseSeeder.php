<?php

namespace Modules\Gateway\database\seeders;

use Illuminate\Database\Seeder;


class GatewayDatabaseSeeder extends Seeder
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
//             CourseStatusSeeder::class,

         ]);
    }
}
