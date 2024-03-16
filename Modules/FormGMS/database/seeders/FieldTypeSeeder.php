<?php

namespace Modules\FormGMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fieldTypes = json_decode(file_get_contents(realpath(__DIR__.'/fieldType.json')), true);

        foreach ($fieldTypes as $fieldType) {
            DB::table('field_types')->insertGetId([
                'display_name' => $fieldType['name'],
                'name' => $fieldType['type'],
            ]);
        }
        // $this->call([]);
    }
}
