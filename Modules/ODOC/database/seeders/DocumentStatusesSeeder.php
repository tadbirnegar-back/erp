<?php

namespace Modules\ODOC\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Course;
use Modules\ODOC\app\Models\Document;
use Modules\PFM\app\Models\Booklet;
use Modules\PFM\app\Models\PfmCirculars;

class DocumentStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/DocumentStatusesSeeder.json')), true);

        foreach ($userStatusesData as $userStatus) {
            DB::table('statuses')->updateOrInsert([
                'name' => $userStatus['name'],
                'model' => Document::class,
            ], [
                'name' => $userStatus['name'],
                'model' => Document::class,
                'class_name' => $userStatus['className'] ?? null,
            ]);
        }
    }
}
