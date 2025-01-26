<?php

namespace Modules\HRMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HRMS\app\Http\Traits\EmployeeTrait;
use Modules\HRMS\app\Http\Traits\ScriptTypeTrait;
use Modules\HRMS\app\Models\HireType;
use Modules\HRMS\app\Models\IssueTime;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\Level;
use Modules\HRMS\app\Models\ScriptType;

class FreeZoneScriptTypeSeeder extends Seeder
{
    use EmployeeTrait , ScriptTypeTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scriptTypes = json_decode(file_get_contents(realpath(__DIR__.'/FreeZoneScriptTypeSeeder.json')), true);
        $issueTimeId = IssueTime::where('title' , 'شروع به همکاری')->first()->id;
        $empStatus = $this->activeEmployeeStatus()->id;
        $scriptTypeStatus = $this->activeScriptTypeStatus()->id;
        foreach ($scriptTypes as $scriptType) {
            ScriptType::create([
                "title" => $scriptType['title'],
                'issue_time_id' => $issueTimeId,
                'origin_id' => 1 ,
                'employee_status_id' => $empStatus,
                'status_id' => $scriptTypeStatus,
                'isHeadable' => 0 ,
                'duration' => null
            ]);
        }
        // $this->call([]);
    }
}
