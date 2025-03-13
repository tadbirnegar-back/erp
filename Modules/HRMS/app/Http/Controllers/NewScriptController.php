<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\HRMS\app\Http\Traits\EmployeeTrait;
use Modules\HRMS\app\Http\Traits\NewReqScriptTrait;
use Modules\HRMS\app\Models\HireType;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\Position;
use Modules\HRMS\app\Models\ScriptType;

class NewScriptController extends Controller
{
    use NewReqScriptTrait, EmployeeTrait;

    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function indexVillage(Request $request): JsonResponse
    {
        $data = $request->all();

        $result = $this->LiveSearch($data);

        return response()->json($result);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function storeSarParast(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = Auth::user();
            $employee = $user->employee;
            $hireType = HireType::where('title', 'تمام وقت')->first();
            $scriptType = ScriptType::where('title', 'انتصاب سرپرست دهیاری')->first();
            $job = Job::where('title', 'دهیار')->first();

            $result = $this->getScriptAgentCombos($hireType, $scriptType);

            $sas = $result->map(function ($item) {
                return [
                    'scriptAgentID' => $item->id,
                    'defaultValue' => $item->pivot->default_value ?? 0,
                ];
            });
            $encodedSas = json_encode($sas->toArray());
            $data['hireTypeID'] = $hireType->id;
            $data['scriptTypeID'] = $scriptType->id;
            $data['jobID'] = $job->id;
            $data['operatorID'] = $user->id;
            $data['scriptAgents'] = $encodedSas;
            $data['positionID'] = Position::where('name', 'سرپرست دهیاری')->first()->id;
            $pendingRsStatus = $this->pendingRsStatus();

            $rsRes = $this->rsSingleStore($data, $employee->id, $pendingRsStatus);

            DB::commit();
            return response()->json($rsRes);
        } catch (\Exception $e) {

            return response()->json(['message' => 'خطا در افزودن حکم', $e->getMessage(), $e->getTrace()], 500);
        }

    }


    public function storeheyat(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = Auth::user();
            $employee = $user->employee;
            $hireType = HireType::where('title', 'تمام وقت')->first();
            $scriptType = ScriptType::where('title', 'انتصاب هیئت تطبیق')->first();
            $job = Job::where('title', 'دهیار')->first();

            $result = $this->getScriptAgentCombos($hireType, $scriptType);

            $sas = $result->map(function ($item) {
                return [
                    'scriptAgentID' => $item->id,
                    'defaultValue' => $item->pivot->default_value ?? 0,
                ];
            });
            $encodedSas = json_encode($sas->toArray());
            $data['hireTypeID'] = $hireType->id;
            $data['scriptTypeID'] = $scriptType->id;
            $data['jobID'] = $job->id;
            $data['operatorID'] = $user->id;
            $data['scriptAgents'] = $encodedSas;
            $data['positionID'] = Position::where('id', $data['positionID'])->first()->id;
            $pendingRsStatus = $this->pendingRsStatus();

            $rsRes = $this->rsSingleStore($data, $employee->id, $pendingRsStatus);

            DB::commit();
            return response()->json($rsRes);
        } catch (\Exception $e) {
            return response()->json(['message' => 'خطا در افزودن حکم', $e->getMessage(), $e->getTrace()], 500);
        }

    }

    public function districtsDropDown()
    {

        $result = $this->DropDown();

        return response()->json($result);
    }


}
