<?php

namespace Modules\ProductMS\database\seeders;

use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__.'/unit.json')), true);

        foreach ($userStatusesData as $userStatus) {
            \DB::table('units')->insertGetId([
                'name' => $userStatus['name'],
                'symbol' => $userStatus['symbol'],
            ]);
        }
        // $this->call([]);
    }
}
