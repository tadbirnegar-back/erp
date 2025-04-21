<?php

namespace Modules\PFM\database\seeders;

use Illuminate\Database\Seeder;
use Modules\PFM\database\seeders\PfmCircularStatusesSeeder;

class PFMDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PfmCircularStatusesSeeder::class,
            LevyStatusSeeder::class,
            LeviesSeeder::class,
            BookletStatusesSeeder::class,
            FullFillApplicationsSeeder::class,
            PermissionSeeder::class,
        ]);
    }
}
