<?php

namespace Modules\LMS\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Course;

class AnswerSheetStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/AnswerSheetStatusSeeder.json')), true);

        foreach ($userStatusesData as $userStatus) {
            DB::table('statuses')->updateOrInsert([
                'name' => $userStatus['name'],
                'model' => AnswerSheet::class,
            ], [
                'name' => $userStatus['name'],
                'model' => AnswerSheet::class,
                'class_name' => $userStatus['className'] ?? null,
            ]);
        }
        // $this->call([]);
    }
}
