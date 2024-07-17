<?php

namespace Modules\HRMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HRMS\app\Models\HireType;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\Level;

class HireTypeStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__.'/hireTypeStatusSeeder.json')), true);

        foreach ($userStatusesData as $userStatus) {
            \DB::table('statuses')->insertGetId([
                'name' => $userStatus['name'],
                'model' => HireType::class,
            ]);
        }
        // $this->call([]);
    }
}
