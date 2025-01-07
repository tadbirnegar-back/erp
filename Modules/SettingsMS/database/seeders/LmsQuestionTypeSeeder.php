<?php

namespace Modules\SettingsMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LmsQuestionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $qTypes = json_decode(file_get_contents(realpath(__DIR__ . '/LmsQuestionType.json')), true);
        foreach ($qTypes as $qType) {
            DB::table('settings')->updateOrInsert([
                'key' => $qType['key'],
                'value' => $qType['value'],
            ]);
        }
        // $this->call([]);
    }

}
