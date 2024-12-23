<?php

namespace Modules\LMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\LMS\app\Models\Exam;

class ExamStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $examStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/ExamStatusSeeder.json')), true);

        foreach ($examStatusesData as $examStatus) {
            DB::table('statuses')->updateOrInsert([
                'name' => $examStatus['name'],
                'model' => Exam::class,
            ], [
                'name' => $examStatus['name'],
                'model' => Exam::class,
                'class_name' => $examStatus['className'] ?? null,
            ]);
        }

    }

}
