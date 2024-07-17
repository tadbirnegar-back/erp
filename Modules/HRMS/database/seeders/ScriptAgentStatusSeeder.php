<?php

namespace Modules\HRMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\Level;
use Modules\HRMS\app\Models\ScriptAgent;

class ScriptAgentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__.'/ScriptAgentStatusSeeder.json')), true);

        foreach ($userStatusesData as $userStatus) {
            \DB::table('statuses')->insertGetId([
                'name' => $userStatus['name'],
                'model' => ScriptAgent::class,
            ]);
        }
        // $this->call([]);
    }
}
