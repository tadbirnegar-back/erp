<?php

namespace Modules\FormGMS\database\seeders;

use Illuminate\Database\Seeder;

class FormGMSDatabaseSeeder extends Seeder
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
            FieldTypeSeeder::class,
            FormStatusSeeder::class,
            FieldStatusSeeder::class,
            OptionStatusSeeder::class,

        ]);
        // $this->call([]);
    }
}
