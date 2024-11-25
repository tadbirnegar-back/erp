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
use Modules\HRMS\app\Models\HireType;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\Position;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptType;
use Modules\HRMS\app\Notifications\DeclineRsNotification;
use Modules\HRMS\app\Notifications\NewRsNotification;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\TownOfc;
use Modules\OUnitMS\app\Models\VillageOfc;
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

            $script->load('approvers.status', 'approvers.assignedTo', 'scriptType', 'hireType', 'position.levels', 'level', 'scriptAgents', 'employee.person', 'latestStatus', 'organizationUnit.ancestors', 'job', 'files', 'rejectReason.person.avatar');
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
            'employeeID' => ['required', 'exists:employees,id'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            DB::beginTransaction();
            $employee = Employee::findOr($data['employeeID'], function () use ($data) {
                return response(['message' => 'موردی یافت نشد', 'id' => $data['employeeID']], 404);

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

//            if ($pendingRsStatus) {
//                collect($rsRes)->each(fn($rs) => $this->approvingStore($rs));
//            }

            $employee = Employee::find($rsRes->employee_id);
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
            $this->updateRcFinishDate($script, now());
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


            $terminateStatus = $this->terminatedRsStatus();
            $this->attachStatusToRs($script, $terminateStatus, $request->description ?? null, $user);
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
        $oldRS = RecruitmentScript::with('latestStatus')->find($id);
        if (is_null($oldRS)) {
            return response()->json(['message' => 'حکم مورد نظر یافت نشد'], 404);
        }

        if ($oldRS->latestStatus->id != $this->pendingRsStatus()->id) {
            return response()->json(['message' => 'حکم قابل اصلاح نمی باشد'], 400);
        }

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


        try {
            DB::beginTransaction();

            $this->attachStatusToRs($oldRS, $rejectedRsStatus);

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


            $employee = Employee::find($rsRes->employee_id);
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

    public function RejectRecruitmentScript(Request $request, $id)
    {

        $user = auth()->user();


        /**
         * @var RecruitmentScript $script
         */
        $script = RecruitmentScript::with('approvers')->find($id);

        if (is_null($script)) {
            return response()->json(['message' => 'حکم مورد نظر یافت نشد'], 404);
        }

        try {
            DB::beginTransaction();


            $result = $this->declineScript($script, $user, true, $request->description ?? null);

            $this->updateRcFinishDate($script, now());

            $notifibleUser = $script->user;

            return response()->json($notifibleUser);

            $person = Person::find($notifibleUser->person_id);

            $notifibleUser->notify(new DeclineRsNotification($person->display_name));

            DB::commit();


            return response()->json([
                "result" => $result
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'خطا در رد حکم'], 500);
        }
    }

    public function approveRecruitmentScript($id)
    {
        $script = RecruitmentScript::with('approvers')->find($id);
        if (is_null($script)) {
            return response()->json(['message' => 'حکم مورد نظر یافت نشد'], 404);
        }

        try {
            DB::beginTransaction();

            $result = $this->approveScript($script, Auth::user(), true);
            DB::commit();

            return response()->json([
                "result" => $result
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در تایید حکم', $e->getMessage(), $e->getTrace()], 500);
        }
    }


    public function ptpIndex(Request $request)
    {
        $data = [
            'statusID' => $this->pendingTerminateRsStatus()->id,
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

    public function getMyVillageScripts()
    {
        $user = Auth::user();

        $user->load(['activeRecruitmentScripts' => function ($query) {
            $query->whereHas('ounit', function ($query) {
                $query->where('unitable_type', VillageOfc::class);
            })
                ->with(['ounit.unitable', 'ounit.ancestorsAndSelf' => function ($query) {
                    $query->where('unitable_type', '!=', StateOfc::class);
                }]);
        }, 'person.avatar']);

        return response()->json(['villages' => $user->activeRecruitmentScripts->pluck('ounit'), 'person' => $user->person]);
    }

    public function getVillageOfcByAbadiCode(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'abadiCode' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $abadiCode = $request->input('abadiCode');
        $village = VillageOfc::with('organizationUnit.ancestors', 'organizationUnit.person.avatar')->where('abadi_code', $abadiCode)->first();

        if (is_null($village)) {
            return response()->json(['message' => 'روستایی با کد آبادی مورد نظر یافت نشد'], 404);
        }

        if ($village->organizationUnit->head_id == Auth::user()->id) {
            return response()->json(['message' => 'این روستا قبلا برای شما ثبت شده است'], 400);
        }

        return response()->json($village);
    }

    public function addNewScriptForDehyar(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = Auth::user();
            $employee = $user->employee;
//        $rs = json_decode($data['recruitmentScripts'], true);
            $hireType = HireType::where('title', 'تمام وقت')->first();
            $scriptType = ScriptType::where('title', 'انتصاب دهیار')->first();
            $job = Job::where('title', 'دهیار')->first();

            $result = $this->getScriptAgentCombos($hireType, $scriptType);


//        foreach ($rs as &$script) {

//                    $files = File::find([$script['enactmentAttachmentID'], $script['scriptAttachmentID']]);
//                    $files->each(function ($file) use ($user) {
//                        $file->creator_id = $user->id;
//                        $file->save();
//                    });

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
            $data['positionID'] = Position::where('name', 'دهیار')->first()->id;
//        }
            $pendingRsStatus =
//                    $scriptType->employeeStatus->name == self::$pendingEmployeeStatus
//                    ?
                $this->pendingRsStatus();
//                    : null;

            $rsRes = $this->rsSingleStore($data, $employee->id, $pendingRsStatus);

//            if ($pendingRsStatus) {
//                collect($rsRes)->each(fn($rs) => $this->approvingStore($rs));
//            }
            DB::commit();
            return response()->json($rsRes);
        } catch (\Exception $e) {
            return response()->json(['message' => 'خطا در افزودن حکم', $e->getMessage(), $e->getTrace()], 500);
        }

    }

    public function ptpShow($id)
    {
        $rs = RecruitmentScript::whereHas('status', function ($query) {
            $query->where('status_id', $this->pendingTerminateRsStatus()->id);
        })->find($id);
        return response()->json($rs);
    }

    public function ptpTerminate(Request $request, $id)
    {

        try {
            DB::beginTransaction();
            $script = RecruitmentScript::whereHas('status', function ($query) {
                $query->where('status_id', $this->pendingTerminateRsStatus()->id);
            })->with('latestStatus', 'user')->find($id);
            $terminateStatus = $this->terminatedRsStatus();

            if (is_null($script)) {
                return response()->json(['message' => 'حکم مورد نظر یافت نشد'], 404);
            }

            if ($script->latestStatus->id == $terminateStatus->id) {
                return response()->json(['message' => 'حکم از قبل قطع همکاری شده است'], 400);
            }
            $user = Auth::user();

            $this->updateRcFinishDate($script, $request->date);

            $this->attachStatusToRs($script, $terminateStatus, $request->description ?? null, $user, $request->fileID);

            DB::commit();
            return response()->json([
                'message' => "عزل با موفقیت انجام شد",
                "script" => $script,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در قطع همکاری حکم'], 500);

        }
    }

}
