<?php

namespace Modules\SettingsMS\database\seeders;

use Illuminate\Database\Seeder;

class SettingsMSDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            TimingSeeder::class,
            LmsSettingDifficultySeeder::class,
            LmsQuestionTypeSeeder::class,
            LmsQuestionsNumberSeeder::class,

        ]);
    }
}
