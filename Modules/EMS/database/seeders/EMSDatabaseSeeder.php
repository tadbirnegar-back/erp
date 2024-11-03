<?php

namespace Modules\EMS\database\seeders;

use Illuminate\Database\Seeder;

class EMSDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
//            EnactmentStatusSeeder::class,
//            MeetingStatusSeeder::class,
//            EnactmentReviewStatusSeeder::class,
            EnactmentTitleStatusSeeder::class,

        ]);
    }
}
