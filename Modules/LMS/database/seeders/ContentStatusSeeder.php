<?php

namespace Modules\LMS\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\LMS\app\Models\Content;
use Modules\LMS\app\Models\Course;
class ContentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/ContentStatusSeeder.json')), true);

        foreach ($userStatusesData as $userStatus) {
            DB::table('statuses')->updateOrInsert([
                'name' => $userStatus['name'],
                'model' => Content::class,
            ], [
                'name' => $userStatus['name'],
                'model' => Content::class,
                'class_name' => $userStatus['className'] ?? null,
            ]);
        }
        // $this->call([]);
    }
}
