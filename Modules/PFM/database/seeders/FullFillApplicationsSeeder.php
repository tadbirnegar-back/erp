<?php

namespace Modules\PFM\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Course;
use Modules\PFM\app\Http\Enums\LevyCategoriesEnum;
use Modules\PFM\app\Http\Traits\LevyTrait;
use Modules\PFM\app\Models\Levy;
use Modules\PFM\app\Models\LevyCircular;
use Modules\PFM\app\Models\PfmCirculars;
use Modules\PFM\app\Models\PropApplication;

class FullFillApplicationsSeeder extends Seeder
{
    use LevyTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = json_decode(file_get_contents(realpath(__DIR__ . '/FullFillApplicationsSeeder.json')), true);
        foreach ($applications as $application) {
            PropApplication::create([
                'name' => $application['name'],
                'main_prop_type' => $application['main_prop_type'],
                'adjustment_coefficient' => $application['adjustment_coefficient'],
            ]);
        }

    }

}
