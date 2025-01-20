<?php

namespace Modules\SettingsMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LmsQuestionsNumberSeeder extends Seeder
{
    public function run(): void
    {
        $qNumbers = json_decode(file_get_contents(realpath(__DIR__ . '/LmsQuestionNumber.json')), true);
        foreach ($qNumbers as $qNumber) {
            DB::table('settings')->updateOrInsert([
                'key' => $qNumber['key'],
                'value' => $qNumber['value'],
            ]);
        }
        // $this->call([]);
    }

}
