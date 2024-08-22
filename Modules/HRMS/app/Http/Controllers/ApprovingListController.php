<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Models\RecruitmentScript;

class ApprovingListController extends Controller
{

    use ApprovingListTrait;

    public function showScriptWithApproves($id)
    {
        $user = auth()->user();
        $script = RecruitmentScript::with('scriptType', 'hireType', 'position', 'level', 'scriptAgents', 'approvers.assignedTo', 'approvers.status', 'employee.person', 'latestStatus', 'organizationUnit', 'job')->find($id);

        $canApprove = $script->approvers->where('assigned_to', $user->id)->where('status_id', $this->pendingForCurrentUserStatus()->id)->isNotEmpty();
        $canIssueRevisedScript = false;

        $script->setAttribute('canApprove', $canApprove);
        $script->setAttribute('canIssueRevisedScript', $canIssueRevisedScript);

        return response()->json(['script' => $script]);
    }

    public function approveScriptByUser($id)
    {
        $user = auth()->user();
        $script = RecruitmentScript::find($id);

        $result = $this->approveScript($script, $user);
        if (is_null($result)) {
            return response()->json(['message' => 'You are not allowed to approve this script'], 403);
        }

        return response()->json(['message' => 'Script approved successfully']);
    }
}
