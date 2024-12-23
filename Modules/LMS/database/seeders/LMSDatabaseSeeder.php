<?php

namespace Modules\LMS\database\seeders;

use Illuminate\Database\Seeder;

class LMSDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $this->call([
//             ModuleCategorySeeder::class,
//             ModuleSeeder::class,
//             PermissionSeeder::class,
//            QuestionStatusSeeder::class,
//            OptionStatusSeeder::class,
//            LessonStatusSeeder::class,
            FillCommentsTable::class,
        ]);
    }
}
