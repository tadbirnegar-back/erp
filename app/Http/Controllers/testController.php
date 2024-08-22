<?php

namespace App\Http\Controllers;





use Modules\HRMS\app\Http\Traits\EmployeeTrait;
use Modules\HRMS\app\Models\HireType;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\ScriptType;

class testController extends Controller
{
    use EmployeeTrait;

    public function run()
    {
//        $a= [
//            [
//    "id" => null,
//    "contract" => "9800000",
//    "script_agent_id" => 8,
//    "script_id" => 2209,
//  ]
//];
//         $result = ScriptAgentScript::insert($a);
//        dd($result);



        $hireType = HireType::where('title', 'تمام وقت')->first();
        $scriptType = ScriptType::where('title', 'انتصاب دهیار')->first();

        $job = Job::where('title', 'دهیار')->first();

        $result = $this->getScriptAgentCombos($hireType, $scriptType);
        $x = '[{"ounitID":3869,"positionID":1,"startDate":"2024-08-21 07:40:57","files":[{"id":201,"title":"تصویر حکم"},{"id":202,"title":"تصویر مصوبات"}]}]';
        $rs = json_decode($x, true);
        foreach ($rs as &$script) {

//                    $files = File::find([$script['enactmentAttachmentID'], $script['scriptAttachmentID']]);
//                    $files->each(function ($file) use ($user) {
//                        $file->creator_id = $user->id;
//                        $file->save();
//                    });

            $sas = $result->map(function ($item) {

                return [
                    'scriptAgentID' => $item->id,
                    'defaultValue' => $item->pivot->default_value,
                ];
            });
            $encodedSas = json_encode($sas->toArray());
            $script['hireTypeID'] = $hireType->id;
            $script['scriptTypeID'] = $scriptType->id;
            $script['jobID'] = $job->id;
            $script['operatorID'] = 1905;
            $script['scriptAgents'] = $encodedSas;

        }
        $pendingRsStatus = $scriptType?->employeeStatus?->name == self::$pendingEmployeeStatus
            ? $this->pendingRsStatus()
            : null;

        $rsRes = $this->rsStore($rs, 1905, $pendingRsStatus);

        if ($pendingRsStatus) {
            collect($rsRes)->each(fn($rs) => $this->approvingStore($rs));
        }

    }



}

