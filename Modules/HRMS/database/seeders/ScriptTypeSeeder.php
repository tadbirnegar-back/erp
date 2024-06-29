<?php

namespace Modules\HRMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\IssueTime;
use Modules\HRMS\app\Models\ScriptType;

class ScriptTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scriptTypes = json_decode(file_get_contents(realpath(__DIR__.'/scriptTypes.json')), true);
        $scriptTypes = collect($scriptTypes);
        $scriptTypes=$scriptTypes->map(function ($scriptType) {
            $status= Employee::GetAllStatuses()->firstWhere('name',$scriptType['employee_status']);

            $issueTime=IssueTime::firstWhere('title',$scriptType['issue_time'])->first();
            return [
                'title' => $scriptType['title'],
                'employee_status_id' => $status->id,
                'issue_time_id' => $issueTime->id,
            ];

        });

        ScriptType::insert($scriptTypes->toArray());
        // $this->call([]);
    }
}
