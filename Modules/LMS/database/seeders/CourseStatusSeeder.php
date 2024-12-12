<?php

namespace Modules\LMS\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\LMS\app\Models\Course;

class CourseStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/CourseStatusSeeder.json')), true);

        foreach ($userStatusesData as $userStatus) {
            DB::table('statuses')->updateOrInsert([
                'name' => $userStatus['name'],
                'model' => Course::class,
            ], [
                'name' => $userStatus['name'],
                'model' => Course::class,
                'class_name' => $userStatus['className'] ?? null,
            ]);
        }
        // $this->call([]);
    }
}
