<?php

namespace Modules\LMS\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\LMS\app\Models\Question;

class QuestionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $QuestionStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/questionStatuses.json')), true);

        foreach ($QuestionStatusesData as $questionStatus) {
            DB::table('statuses')->updateOrInsert([
                'name' => $questionStatus['name'],
                'model' => Question::class,
            ], [
                'name' => $questionStatus['name'],
                'model' => Question::class,
                'class_name' => $questionStatus['className'] ?? null,
            ]);
        }
        // $this->call([]);
    }
}
