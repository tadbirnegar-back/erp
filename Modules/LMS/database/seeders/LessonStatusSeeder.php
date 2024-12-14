<?php

namespace Modules\LMS\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Lesson;

class LessonStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/LessonStatusSeeder.json')), true);

        foreach ($userStatusesData as $userStatus) {
            DB::table('statuses')->updateOrInsert([
                'name' => $userStatus['name'],
                'model' => Lesson::class,
            ], [
                'name' => $userStatus['name'],
                'model' => Lesson::class,
                'class_name' => $userStatus['className'] ?? null,
            ]);
        }
        // $this->call([]);
    }
}
