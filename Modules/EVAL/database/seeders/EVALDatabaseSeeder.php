<?php

namespace Modules\EVAL\database\seeders;

use Illuminate\Database\Seeder;

class EVALDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
//            EvalStatusSeeder::class,
//            EvalCircularStatusSeeder::class,
//            MergeLastEvalToNewEvalSeeder::class,
            PermissionSeeder::class,
        ]);
    }
}
