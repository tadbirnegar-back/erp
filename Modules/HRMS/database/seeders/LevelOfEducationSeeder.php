<?php

namespace Modules\HRMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\HRMS\app\Models\LevelOfEducation;

class LevelOfEducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__.'/levelOfEducation.json')), true);

        foreach ($userStatusesData as $userStatus) {
            \DB::table('levels_of_education')->insertGetId([
                'name' => $userStatus['name'],
            ]);
        }
        // $this->call([]);
    }
}
