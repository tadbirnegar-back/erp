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
            $job = Job::where('title', 'سرپرست دهیاری')->first();

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

            return response()->json(['message' => 'خطا در افزودن حکم', 'error', 'error'], 500);
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
            $job = Job::where('title', 'عضو هیئت')->first();

            $result = $this->getScriptAgentCombos($hireType, $scriptType);

            $sas = $result->map(function ($item) {
                return [
                    'scriptAgentID' => $item->id,
                    'defaultValue' => $item->pivot->default_value ?? 0,
                ];
            });
            $positionsName=Position::whereIn('name',['کارشناس مشورتی','نماینده استانداری'])->get();
            $ids=$positionsName->pluck('id')->toArray();
            $dabirId=Position::where('name','مسئول دبیرخانه')->first()->id;
            $dabir=Position::where('name','مسئول دبیرخانه')->first();

            $encodedSas = json_encode($sas->toArray());
            $data['hireTypeID'] = $hireType->id;
            $data['scriptTypeID'] = $scriptType->id;
            $data['jobID'] = $job->id;
            $data['operatorID'] = $user->id;
            $data['scriptAgents'] = $encodedSas;

            if ($dabir && $dabir->id == $data['positionID']) {
                $data['scriptTypeID'] = ScriptType::where('title', 'انتصاب دبیر')->value('id');
            } else {
                $data['scriptTypeID'] = ScriptType::where('title', 'انتصاب هیئت تطبیق')->value('id');
            }



            if (isset($data['positionID']) && in_array($data['positionID'], $ids)) {
                $job = Job::where('title', 'کارشناس مشورتی')->first();
            }
            if ($data['positionID']== $dabirId) {
                $job = Job::where('title', 'مسئول دبیرخانه')->first();
            }
            $data['jobID'] = $job ? $job->id : null;
            $pendingRsStatus = $this->pendingRsStatus();

            $rsRes = $this->rsSingleStore($data, $employee->id, $pendingRsStatus);

            DB::commit();
            return response()->json($rsRes);
        } catch (\Exception $e) {
            return response()->json(['message' => 'خطا در افزودن حکم', 'error', 'error'], 500);
        }

    }

    public function districtsDropDown()
    {

        $result = $this->DropDown();

        return response()->json($result);
    }


}
