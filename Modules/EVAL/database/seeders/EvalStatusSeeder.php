<?php

namespace Modules\EVAL\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\EVAL\app\Models\EvalEvaluation;

class EvalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/EvalStatusSeeder.json')), true);

        foreach ($userStatusesData as $userStatus) {
            DB::table('statuses')->updateOrInsert([
                'name' => $userStatus['name'],
                'model' => EvalEvaluation::class,
            ], [
                'name' => $userStatus['name'],
                'model' => EvalEvaluation::class,
                'class_name' => $userStatus['className'] ?? null,
            ]);
        }
        // $this->call([]);
    }
}
