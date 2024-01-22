<?php

namespace Modules\AddressMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\AddressMS\app\Models\Address;

class AddressStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/addressStatus.json')), true);

        foreach ($userStatusesData as $userStatus) {
            DB::table('statuses')->insertGetId([
                'name' => $userStatus['name'],
                'model' => Address::class,
            ]);
            // $this->call([]);
        }
    }
}
