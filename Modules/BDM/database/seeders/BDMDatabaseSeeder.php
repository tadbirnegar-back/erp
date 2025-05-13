<?php

namespace Modules\BDM\database\seeders;

use Illuminate\Database\Seeder;

class BDMDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            DossierStatusesSeeder::class,
            PermitStatusesSeeder::class,
            PermissionsSeeder::class,
        ]);
    }
}
