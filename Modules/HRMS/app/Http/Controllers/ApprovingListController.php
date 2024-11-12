<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Modules\HRMS\app\Http\Enums\ScriptStatusEnum;
use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Notifications\ApproveRsNotification;
use Modules\HRMS\App\Notifications\DeclineRsNotification;
use Modules\PersonMS\app\Models\Person;

class ApprovingListController extends Controller
{

    use ApprovingListTrait;

    public function showScriptWithApproves($id)
    {
        $user = auth()->user();
        $script = RecruitmentScript::with('scriptType', 'hireType', 'position', 'level', 'scriptAgents', 'approvers.assignedTo', 'approvers.status', 'employee.person', 'latestStatus', 'organizationUnit.ancestors', 'job', 'files')->find($id);

        $canApprove = $script->approvers->where('assigned_to', $user->id)->where('status_id', $this->pendingForCurrentUserStatus()->id)->isNotEmpty();
        $canIssueRevisedScript = false;

        $script->setAttribute('canApprove', $canApprove);
        $script->setAttribute('canIssueRevisedScript', $canIssueRevisedScript);

        return response()->json(['script' => $script]);
    }

    public function approveScriptByUser($id)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();
            $script = RecruitmentScript::find($id);

            $canApprove = $script->approvers->where('assigned_to', $user->id)->where('status_id', $this->pendingForCurrentUserStatus()->id)->isNotEmpty();


            if (!$canApprove) {
                return response()->json(['message' => 'شما دسترسی لازم برای تایید حکم را ندارید'], 403);
            }

            $result = $this->approveScript($script, $user);

            $rcstatus = $script->latestStatus;


            $employee = Employee::find($script->employee_id);
            $Notifibleuser = $employee->user;

            $person = Person::find($Notifibleuser->person_id);
            if ($rcstatus->name == ScriptStatusEnum::TAIED->value) {
                $Notifibleuser->notify(new ApproveRsNotification($person->display_name));
            }
            DB::commit();
            return response()->json(['message' => 'حکم با موفقیت تایید شد']);


        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در تایید حکم', 'sth' => $e], 500);
        }
    }

    public function declineScriptByUser($id)
    {

        $user = auth()->user();


        /**
         * @var RecruitmentScript $script
         */
        $script = RecruitmentScript::with('approvers')->find($id);


        if ($script) {
            DB::beginTransaction();


            $approvers = $script->approvers;


            $canApprove = $approvers->where('assigned_to', $user->id)->where('status_id', $this->pendingForCurrentUserStatus()->id)->isNotEmpty();
            if (!$canApprove) {
                return response()->json(['message' => 'شما دسترسی لازم برای تایید حکم را ندارید'], 403);
            }

            $result = $this->declineScript($script, $user);

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


