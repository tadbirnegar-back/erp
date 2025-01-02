<?php


namespace Modules\LMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;

class OucPropertiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $properties = json_decode(file_get_contents(realpath(__DIR__ . '/OucPropertiesSeeder.json')), true);

        foreach ($properties as $property) {
            $ounit_cat_id = OunitCategoryEnum::getDesiredLabelWithValue($property['ounit_cat_name']);
            DB::table('ouc_properties')->updateOrInsert(
                [
                    'ounit_cat_id' => $ounit_cat_id,
                    'column_name' => $property['column_name'],
                    'name' => $property['name'],
                ],
                [
                    'ounit_cat_id' => $ounit_cat_id,
                    'column_name' => $property['column_name'],
                    'name' => $property['name'],
                ]
            );
        }

    }
}
