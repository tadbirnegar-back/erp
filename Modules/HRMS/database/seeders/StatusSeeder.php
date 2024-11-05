<?php

namespace Modules\HRMS\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\HRMS\app\Models\RecruitmentScript;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/StatusSeeder.json')), true);

        foreach ($userStatusesData as $userStatus) {
            DB::table('statuses')->updateOrInsert([
                'name' => $userStatus['name'],
                'model' => RecruitmentScript::class,
            ], [
                'name' => $userStatus['name'],
                'model' => RecruitmentScript::class,
            ]);
        }
        // $this->call([]);
    }
}
