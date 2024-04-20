<?php

namespace Modules\HRMS\database\seeders;

use Illuminate\Database\Seeder;

class MilitaryServiceStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__.'/militaryServiceStatus.json')), true);

        foreach ($userStatusesData as $userStatus) {
            \DB::table('military_service_statuses')->insertGetId([
                'name' => $userStatus['name'],
            ]);
        }
        // $this->call([]);
    }
}
