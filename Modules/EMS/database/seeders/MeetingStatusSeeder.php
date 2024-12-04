<?php

namespace Modules\EMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\EMS\app\Models\Meeting;

class MeetingStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/MeetingStatus.json')), true);

        foreach ($userStatusesData as $userStatus) {
            DB::table('statuses')->insertGetId([
                'name' => $userStatus['name'],
                'model' => Meeting::class,
            ]);
        }
        // $this->call([]);
    }
}
