<?php

namespace Modules\CustomerMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\CustomerMS\app\Models\Customer;

class CustomerStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__.'/customerStatus.json')), true);

        foreach ($userStatusesData as $userStatus) {
            DB::table('statuses')->insertGetId([
                'name' => $userStatus['name'],
                'model' => Customer::class,
            ]);
        }
        // $this->call([]);
    }
}
