<?php


namespace Modules\LMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
use Modules\LMS\app\Http\Traits\OucPropertyTrait;

class OucPropertyValues extends Seeder
{
    use OucPropertyTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $propertyValues = json_decode(file_get_contents(realpath(__DIR__ . '/OucPropertyValues.json')), true);

        foreach ($propertyValues as $propertyValue) {
            $ouc_property_id = $this->getOucPropertyIdByName($propertyValue['ouc_property_name']);
            DB::table('ouc_property_values')->updateOrInsert(
                [
                    'ouc_property_id' => $ouc_property_id,
                    'value' => $propertyValue['value'],
                    'operator' => $propertyValue['operator'],
                ],
                [
                    'ouc_property_id' => $ouc_property_id,
                    'value' => $propertyValue['value'],
                    'operator' => $propertyValue['operator'],
                ]
            );
        }

    }
}
