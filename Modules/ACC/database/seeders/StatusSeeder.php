<?php

namespace Modules\ACC\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\ACC\app\Models\Account;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/accountStatus.json')), true);

        foreach ($userStatusesData as $userStatus) {
            DB::table('statuses')->updateOrInsert([
                'name' => $userStatus['name'],
                'class_name' => $userStatus['className'],
                'model' => Account::class,
            ]);
        }
    }
}
