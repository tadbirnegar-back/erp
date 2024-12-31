<?php

namespace Modules\ACMS\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\ACMS\app\Models\Budget;

class BudgetStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/budgetStatus.json')), true);

        foreach ($userStatusesData as $userStatus) {
            DB::table('statuses')->updateOrInsert([
                'name' => $userStatus['name'],
                'class_name' => $userStatus['className'],
                'model' => Budget::class,
            ]);
        }
    }
}
