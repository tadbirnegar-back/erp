<?php

namespace Modules\EvalMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = json_decode(file_get_contents(realpath(__DIR__.'/modules.json')), true);

        foreach ($modules as $module) {
            $moduleCat=\DB::table('module_categories')->where('name','=',$module['category_name'])->get('id')->first();
            DB::table('modules')->insertGetId([
                'name' => $module['name'],
                'module_category_id' => $moduleCat->id,
            ]);
        }
        // $this->call([]);
    }
}
