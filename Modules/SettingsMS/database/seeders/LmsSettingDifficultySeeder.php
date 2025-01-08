<?php

namespace Modules\SettingsMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LmsSettingDifficultySeeder extends Seeder
{
    public function run(): void
    {
        $difficulties = json_decode(file_get_contents(realpath(__DIR__ . '/LmsDifficulty.json')), true);
        foreach ($difficulties as $difficulty) {
            DB::table('settings')->updateOrInsert([
                'key' => $difficulty['key'],
                'value' => $difficulty['value'],
            ]);
        }
        // $this->call([]);
    }
}
