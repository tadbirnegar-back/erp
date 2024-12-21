<?php


namespace Modules\LMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\LMS\app\Models\Question;

class OptionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $OptionStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/OptionStatus.json')), true);

        foreach ($OptionStatusesData as $optionStatus) {
            DB::table('statuses')->updateOrInsert([
                'name' => $optionStatus['name'],
                'model' => Question::class,
            ], [
                'name' => $optionStatus['name'],
                'model' => Question::class,
                'class_name' => $optionStatus['className'] ?? null,
            ]);
        }
    }
}
