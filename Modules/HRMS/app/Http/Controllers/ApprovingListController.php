<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Models\RecruitmentScript;

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

            DB::commit();
            return response()->json(['message' => 'حکم با موفقیت تایید شد']);


        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در تایید حکم'], 500);
        }

    }
}
