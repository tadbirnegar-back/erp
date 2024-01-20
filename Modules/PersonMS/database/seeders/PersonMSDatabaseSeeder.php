<?php

namespace Modules\PersonMS\database\seeders;

use Illuminate\Database\Seeder;

class PersonMSDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {        $this->call([
        ModuleCategorySeeder::class,
        ModuleSeeder::class,
        PermissionSeeder::class,
    ]);
        // $this->call([]);
    }
}
