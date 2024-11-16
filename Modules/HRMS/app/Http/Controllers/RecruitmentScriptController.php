<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Modules\AAA\app\Http\Traits\UserTrait;
use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Http\Traits\EmployeeTrait;
use Modules\HRMS\app\Http\Traits\HireTypeTrait;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Http\Traits\ScriptTypeTrait;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\RecruitmentScriptStatus;
use Modules\HRMS\app\Models\ScriptType;
use Modules\HRMS\App\Notifications\DeclineRsNotification;
use Modules\HRMS\app\Notifications\NewRsNotification;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\TownOfc;
use Modules\PersonMS\app\Models\Person;

class RecruitmentScriptController extends Controller
{
    use RecruitmentScriptTrait, ApprovingListTrait, HireTypeTrait, ScriptTypeTrait, EmployeeTrait, UserTrait;

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

            $script->load('scriptType', 'hireType', 'position', 'level', 'scriptAgents', 'employee.person', 'latestStatus', 'organizationUnit.ancestors', 'job', 'files', 'rejectReason');
        } else {
            return response()->json(['message' => 'شما به این بخش دسترسی ندارید'], 403);
        }


        return response()->json(['script' => $script, 'components' => $this->getComponentsToRenderSinglePage($script, $user)]);

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

            $scriptType = ScriptType::with('employeeStatus')->find($data['scriptTypeID']);

//            if (isset($data['parentID'])) {
//
//
//                $this->changeParentRecruitmentScriptStatus($employee, $data['parentID'], $scriptType->issueTime);
//
//            }

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


            $person = Person::find($user->person_id);

            $user->notify(new NewRsNotification($person->display_name));


            DB::commit();


            return response()->json($rsRes);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در افزودن حکم'], 500);
        }
    }

    public function cancelRscript(Request $request, $id)
    {
        $script = RecruitmentScript::with('latestStatus', 'user')->find($id);
        $cancelStatus = $this->cancelRsStatus();

        if (is_null($script)) {
            return response()->json(['message' => 'حکم مورد نظر یافت نشد'], 404);
        }

        if ($script->latestStatus->id == $cancelStatus->id) {
            return response()->json(['message' => 'حکم از قبل لغو شده است'], 400);
        }
        $user = Auth::user();

        try {
            DB::beginTransaction();
            $this->attachStatusToRs($script, $cancelStatus, $request->description ?? null, $user);
            $this->detachRolesByPosition($script->user, $script->position_id);
            DB::commit();
            return response()->json(['message' => 'حکم با موفقیت لغو شد']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در لغو حکم'], 500);

        }


    }

    public function terminateRscript(Request $request, $id)
    {
        $script = RecruitmentScript::with('latestStatus', 'user')->find($id);
        $terminateStatus = $this->terminatedRsStatus();

        if (is_null($script)) {
            return response()->json(['message' => 'حکم مورد نظر یافت نشد'], 404);
        }

        if ($script->latestStatus->id == $terminateStatus->id) {
            return response()->json(['message' => 'حکم از قبل قطع همکاری شده است'], 400);
        }
        $user = Auth::user();

        try {
            DB::beginTransaction();
            $this->attachStatusToRs($script, $terminateStatus, $request->description ?? null, $user);
            $this->detachRolesByPosition($script->user, $script->position_id);
            DB::commit();
            return response()->json(['message' => 'حکم با موفقیت قطع همکاری شد']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در قطع همکاری حکم'], 500);

        }


    }

    public function endOfServiceRscript(Request $request, $id)
    {
        $script = RecruitmentScript::with('latestStatus', 'user')->find($id);
        $terminateStatus = $this->endOfServiceRsStatus();

        if (is_null($script)) {
            return response()->json(['message' => 'حکم مورد نظر یافت نشد'], 404);
        }

        if ($script->latestStatus->id == $terminateStatus->id) {
            return response()->json(['message' => 'حکم از قبل قطع همکاری شده است'], 400);
        }
        $user = Auth::user();

        try {
            DB::beginTransaction();
            $this->attachStatusToRs($script, $terminateStatus, $request->description ?? null, $user);
            $this->detachRolesByPosition($script->user, $script->position_id);
            DB::commit();
            return response()->json(['message' => 'حکم با موفقیت قطع همکاری شد']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در قطع همکاری حکم'], 500);

        }


    }

    public function expireRscript($id)
    {
        $script = RecruitmentScript::with('latestStatus', 'user')->find($id);
        $terminateStatus = $this->expiredRsStatus();

        if (is_null($script)) {
            return response()->json(['message' => 'حکم مورد نظر یافت نشد'], 404);
        }

        if ($script->latestStatus->id == $terminateStatus->id) {
            return response()->json(['message' => 'حکم از قبل قطع همکاری شده است'], 400);
        }

        try {
            DB::beginTransaction();
            $this->attachStatusToRs($script, $terminateStatus);
            $this->detachRolesByPosition($script->user, $script->position_id);
            DB::commit();
            return response()->json(['message' => 'حکم با موفقیت قطع همکاری شد']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در قطع همکاری حکم'], 500);

        }


    }

    public function renewScript(Request $request, $id)
    {
        $script = RecruitmentScript::find($id);
        $user = Auth::user();
        if (is_null($script)) {
            return response()->json(['message' => 'حکم مورد نظر یافت نشد'], 404);
        }
        try {
            DB::beginTransaction();
            $RS['ounitID'] = $script->organization_unit_id;
            $RS['levelID'] = $script->level_id;
            $RS['positionID'] = $script->position_id;
            $RS['description'] = $script->description;
            $RS['hireTypeID'] = $script->hire_type_id;
            $RS['jobID'] = $script->job_id;
            $RS['operatorID'] = $user->id;
            $RS['scriptTypeID'] = $script->script_type_id;
            $RS['parentID'] = $script->id;
            $RS['startDate'] = $request->startDate;
            $RS['expireDate'] = $request->expireDate;

            $pendingRsStatus = $this->pendingRsStatus();

            $rsRes = $this->rsSingleStore($RS, $script->employee_id, $pendingRsStatus);

            if ($pendingRsStatus) {
                collect($rsRes)->each(fn($rs) => $this->approvingStore($rs));
            }
            $terminateStatus = $this->terminatedRsStatus();
//            $this->attachStatusToRs($script, $terminateStatus, $request->description ?? null, $user);
            DB::commit();
            return response()->json($rsRes);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در تمدید حکم'], 500);

        }

    }


    public function RenewRecruitmentScript(Request $request, $id)
    {
        $data = $request->all();

        $data['parentID'] = $id;
        $validator = Validator::make($data, [
            'employeeID' => 'required',
            'parentID' => ['required',
                'exists:recruitment_scripts,id'
            ],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $rejectedRsStatus = $this->rejectedRsStatus();


        RecruitmentScriptStatus::create([
            'recruitment_script_id' => $id,
            'status_id' => $rejectedRsStatus->id,
        ]);


        try {
            DB::beginTransaction();
            $employee = Employee::findOr($data['employeeID'], function () {
                return response(['message' => 'موردی یافت نشد'], 404);

            });

            $scriptType = ScriptType::with('employeeStatus')->find($data['scriptTypeID']);

//            if (isset($data['parentID'])) {
//
//
//                $this->changeParentRecruitmentScriptStatus($employee, $data['parentID'], $scriptType->issueTime);
//
//            }

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


            $person = Person::find($user->person_id);

            $user->notify(new NewRsNotification($person->display_name));


            DB::commit();


            return response()->json($rsRes);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در افزودن حکم'], 500);
        }
    }


    public function RejectRecruitmentScript($id)
    {

        $user = auth()->user();


        /**
         * @var RecruitmentScript $script
         */
        $script = RecruitmentScript::with('approvers')->find($id);


        if ($script) {
            \Illuminate\Support\Facades\DB::beginTransaction();


            $approvers = $script->approvers;


            $canApprove = $approvers->where('assigned_to', $user->id)->where('status_id', $this->pendingForCurrentUserStatus()->id)->isNotEmpty();
            if (!$canApprove) {
                return response()->json(['message' => 'شما دسترسی لازم برای تایید حکم را ندارید'], 403);
            }

            $result = $this->declineScript($script, $user, true);

            $rcstatus = $script->latestStatus;
            $employee = Employee::find($script->employee_id);


            $notifibleUser = $employee->user;

            $person = Person::find($notifibleUser->person_id);

            $notifibleUser->notify(new DeclineRsNotification($person->display_name));

            DB::commit();


            return response()->json([
                "result" => $result
            ]);
        } else {
            DB::rollBack();

            return response()->json(['message' => 'Script not found'], 404);
        }
    }

}
