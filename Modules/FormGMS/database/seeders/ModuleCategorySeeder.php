<?php

namespace Modules\FormGMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $moduleCategoriesData = json_decode(file_get_contents(realpath(__DIR__.'/moduleCategories.json')), true);

        foreach ($moduleCategoriesData as $moduleCategory) {
            DB::table('module_categories')->insertGetId([
                'name' => $moduleCategory['name'],
                'icon' => $moduleCategory['icon'],
            ]);
        }
        // $this->call([]);
    }
}
