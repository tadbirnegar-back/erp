<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Http\Traits\EmployeeTrait;
use Modules\HRMS\app\Http\Traits\HireTypeTrait;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Http\Traits\ScriptTypeTrait;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptType;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\TownOfc;

class RecruitmentScriptController extends Controller
{
    use RecruitmentScriptTrait, ApprovingListTrait, HireTypeTrait, ScriptTypeTrait, EmployeeTrait;

    public function stateOfcs(Request $request)
    {
        $states = StateOfc::with('organizationUnit')
            //exclude EastAzerbaijan state from loading
            ->whereIntegerNotInRaw('id', [3])
            ->get();

        return response()->json($states);

    }

    public function cityOfcs(Request $request)
    {
        $states = CityOfc::with('organizationUnit')->where('state_ofc_id', $request->stateOfcID)->get();

        return response()->json($states);

    }

    public function districtOfcs(Request $request)
    {
        $states = DistrictOfc::with('organizationUnit')->where('city_ofc_id', $request->cityOfcID)->get();

        return response()->json($states);

    }

    public function townOfcs(Request $request)
    {
        $states = TownOfc::with('organizationUnit')->where('district_ofc_id', $request->districtOfcID)->get();

        return response()->json($states);

    }


    public function villageOfcs(Request $request)
    {
        $districtOfc = DistrictOfc::with(['villageOfcs' => function ($query) {
            $query->where('hasLicense', true)->with('organizationUnit');
        }])->find($request->districtOfcID);

        return response()->json($districtOfc->villageOfcs);

    }

    public function index(Request $request)
    {
        // Extract parameters from the request
        $data = [
            'statusID' => $request->input('statusID'),
            'scriptTypeID' => $request->input('scriptTypeID'),
            'perPage' => $request->input('perPage', 10), // Default to 10 if not provided
            'pageNum' => $request->input('pageNum', 1), // Default to 1 if not provided
            'name' => $request->input('name')
        ];


        $result = $this->rsIndex($data);

        $filterData = $data['pageNum'] == 1 ? [
            'scriptStatus' => RecruitmentScript::GetAllStatuses(),
            'scriptTypes' => $this->getListOfScriptTypes(),
        ] : null;

        return response()->json(['data' => $result, 'filter' => $filterData]);
    }

    public function indexExpiredScripts(Request $request)
    {
        $data = [
            'statusID' => $this->expiredRsStatus()->id,
            'scriptTypeID' => $request->input('scriptTypeID'),
            'perPage' => $request->input('perPage', 10),
            'pageNum' => $request->input('pageNum', 1),
            'name' => $request->input('name')
        ];

        $result = $this->rsIndex($data);

        $filterData = $data['pageNum'] == 1 ? [
            'scriptStatus' => RecruitmentScript::GetAllStatuses(),
            'scriptTypes' => $this->getListOfScriptTypes(),
        ] : null;

        return response()->json(['data' => $result, 'filter' => $filterData]);
    }


    public function recruitmentScriptShow(Request $request, $id)
    {
        $script = RecruitmentScript::with('employee.person')->find($id);
        if (is_null($script)) {
            return response()->json(['message' => 'موردی یافت نشد'], 404);
        }
        $user = Auth::user();
        $requestedRoute = $request->route()->uri();
        $requestedRoute = str_replace('api/v1', '', $requestedRoute);
        $requestedRoute = str_replace('\\', '', $requestedRoute);

        if ($user->hasPermissionForRoute($requestedRoute) || $script->employee->person->id == $user->person->id) {

            $script->load('scriptType', 'hireType', 'position', 'level', 'scriptAgents', 'employee.person', 'latestStatus', 'organizationUnit.ancestors', 'job', 'files');
        } else {
            return response()->json(['message' => 'شما به این بخش دسترسی ندارید'], 403);
        }


        return response()->json(['script' => $script]);

    }

    public function pendingApprovingIndex()
    {
        $user = auth()->user();
        $result = $this->approvingListPendingIndex($user);
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'employeeID' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            DB::beginTransaction();
            $employee = Employee::findOr($data['employeeID'], function () {
                return response(['message' => 'موردی یافت نشد'], 404);

            });

            $scriptType = ScriptType::with('issueTime', 'employeeStatus')->find($data['scriptTypeID']);

            if (isset($data['parentID'])) {


                $this->changeParentRecruitmentScriptStatus($employee, $data['parentID'], $scriptType->issueTime);

            }

            $pendingRsStatus =
//                $scriptType->employeeStatus->name == self::$pendingEmployeeStatus
//                ?
                $this->pendingRsStatus();
//                : null;

            $rsRes = $this->rsSingleStore($data, $employee->id, $pendingRsStatus);

            if ($pendingRsStatus) {
                collect($rsRes)->each(fn($rs) => $this->approvingStore($rs));
            }

            $employee = Employee::find($rsRes[0]->employee_id);
            $user = $employee->user;

            return response()->json($user);

            DB::commit();


            return response()->json($rsRes[0]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در افزودن حکم'], 500);
        }
    }


}
