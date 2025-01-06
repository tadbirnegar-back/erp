<?php

namespace Modules\SettingsMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimingSeeder extends Seeder
{
    public function run(): void
    {
        $timings = json_decode(file_get_contents(realpath(__DIR__ . '/TimingSeeder.json')), true);
        foreach ($timings as $timing) {
            DB::table('settings')->updateOrInsert([
                'key' => $timing['key'],
                'value' => $timing['value'],
            ]);
        }
        // $this->call([]);
    }
}
