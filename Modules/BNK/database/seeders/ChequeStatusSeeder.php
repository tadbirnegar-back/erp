<?php

namespace Modules\BNK\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\BNK\app\Models\Cheque;

class ChequeStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/ChequeStatus.json')), true);

        foreach ($userStatusesData as $userStatus) {
            DB::table('statuses')->updateOrInsert([
                'name' => $userStatus['name'],
                'class_name' => $userStatus['className'],
                'model' => Cheque::class,
            ]);
        }
    }
}
