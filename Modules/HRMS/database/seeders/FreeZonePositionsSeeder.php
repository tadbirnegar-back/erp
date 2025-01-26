<?php

namespace Modules\HRMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
use Modules\HRMS\app\Http\Traits\PositionTrait;
use Modules\HRMS\app\Models\Position;

class FreeZonePositionsSeeder extends Seeder
{
    use PositionTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = json_decode(file_get_contents(realpath(__DIR__.'/FreeZonePositionSeeder.json')), true);
        $status_id = $this->activePositionStatus()->id;
        foreach ($positions as $position) {
            Position::create([
                "name" => $position['name'],
                "ounit_cat" => OunitCategoryEnum::VillageOfc->value,
                "status_id" => $status_id,
            ]);
        }
    }

}
