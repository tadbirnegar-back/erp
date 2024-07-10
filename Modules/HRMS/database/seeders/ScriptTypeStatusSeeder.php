<?php

namespace Modules\HRMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HRMS\app\Models\ScriptType;

class ScriptTypeStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__.'/scriptTypeStatusSeeder.json')), true);

        foreach ($userStatusesData as $userStatus) {
            \DB::table('statuses')->insertGetId([
                'name' => $userStatus['name'],
                'model' => ScriptType::class,
            ]);
        }
        // $this->call([]);
    }
}
