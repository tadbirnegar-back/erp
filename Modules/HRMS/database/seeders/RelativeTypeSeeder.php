<?php

namespace Modules\HRMS\database\seeders;

use Illuminate\Database\Seeder;

class RelativeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__.'/relativeType.json')), true);

        foreach ($userStatusesData as $userStatus) {
            \DB::table('relative_types')->insertGetId([
                'name' => $userStatus['name'],
            ]);
        }
        // $this->call([]);
    }
}
