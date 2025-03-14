<?php

namespace Modules\HRMS\app\RecruitmentScriptStatus;

use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Contracts\StatusHandlerInterface;
use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Models\RecruitmentScript;

class PendingApproveHandler implements StatusHandlerInterface
{

    use ApprovingListTrait;

    private RecruitmentScript $script;
    private ?User $user;

    public function __construct(RecruitmentScript $script, ?User $user = null)
    {
        $this->script = $script;
        $this->user = $user;
    }

    public function execute(): void
    {
        $script = $this->script;
        \DB::transaction(function () use ($script) {
            $this->approvingStore($script);
            $this->deactivateScriptUser();
        });
    }

    public function deactivateScriptUser()
    {
        $disabledStatusForUser = User::GetAllStatuses()->firstWhere('name', '=', 'غیرفعال');
        $scriptUser = $this->script->load('user')->user;
        if(is_null($scriptUser)){
            dd($this->script, $this->script?->user);
        }
        \Log::info($scriptUser);
        $scriptUser->load('roles');
        if ($scriptUser->roles->isEmpty()) {
            $scriptUser->statuses()->attach($disabledStatusForUser->id);
        }
    }
}
