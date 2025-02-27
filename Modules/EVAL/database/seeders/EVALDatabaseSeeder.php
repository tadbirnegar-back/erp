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
             EvalCircularStatusSeeder::class,
         ]);
    }
}
