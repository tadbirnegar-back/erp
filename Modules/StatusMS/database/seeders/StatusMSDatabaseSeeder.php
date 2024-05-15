<?php

namespace Modules\StatusMS\database\seeders;

use Database\Seeders\StatusesTableSeeder;
use Illuminate\Database\Seeder;

class StatusMSDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(StatusesTableSeeder::class);

        // $this->call([]);
    }
}
