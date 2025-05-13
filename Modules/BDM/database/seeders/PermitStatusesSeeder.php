<?php

namespace Modules\BDM\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\BDM\app\Models\PermitStatus;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Course;
use Modules\PFM\app\Models\Booklet;
use Modules\PFM\app\Models\PfmCirculars;

class PermitStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permitStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/PermitStatusesSeeder.json')), true);

        foreach ($permitStatusesData as $permitStatus) {
            DB::table('statuses')->updateOrInsert([
                'name' => $permitStatus['name'],
                'model' => PermitStatus::class,
            ], [
                'name' => $permitStatus['name'],
                'model' => PermitStatus::class,
                'class_name' => $permitStatus['className'] ?? null,
            ]);
        }
    }
}
