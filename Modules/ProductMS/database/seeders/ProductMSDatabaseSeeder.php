<?php

namespace Modules\ProductMS\database\seeders;

use Illuminate\Database\Seeder;

class ProductMSDatabaseSeeder extends Seeder
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
            UnitSeeder::class,
            ProductStatusSeeder::class,
            VariantGroupStatusSeeder::class,
            VariantStatusSeeder::class,
            UnitSeeder::class,

        ]);
        // $this->call([]);
    }
}
