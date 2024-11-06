<?php

namespace Modules\HRMS\database\seeders;

use Illuminate\Database\Seeder;

class HRMSDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
//            ModuleCategorySeeder::class,
//            ModuleSeeder::class,
//            PermissionSeeder::class,
//            LevelOfEducationSeeder::class,
//            LevelStatusSeeder::class,
//            MilitaryServiceStatusSeeder::class,
//            PositionStatusSeeder::class,
//            SkillStatusSeeder::class,
//            WorkForceStatusSeeder::class,
//            RelativeTypeSeeder::class,
//IssueTimesSeeder::class,
//            ScriptTypeSeeder::class,
//            ScriptAgentTypeStatusSeeder::class,
//JobStatusSeeder::class,
//            HireTypeStatusSeeder::class,
//ScriptTypeStatusSeeder::class,
//ScriptAgentStatusSeeder::class,
            PermissionSeeder::class,
            StatusSeeder::class,
        ]);
        // $this->call([]);
    }
}
