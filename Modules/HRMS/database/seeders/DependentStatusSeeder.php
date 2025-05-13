<?php

namespace Modules\HRMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\HRMS\app\Models\Dependent;

class DependentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/DependentStatus.json')), true);

        foreach ($userStatusesData as $userStatus) {
            \DB::table('statuses')->insertGetId([
                'name' => $userStatus['name'],
                'model' => Dependent::class,
            ]);
        }
        // $this->call([]);
    }
}
