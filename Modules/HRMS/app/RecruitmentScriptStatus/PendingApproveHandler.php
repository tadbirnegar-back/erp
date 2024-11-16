<?php

namespace Modules\HRMS\app\RecruitmentScriptStatus;

use Modules\HRMS\app\Contracts\StatusHandlerInterface;
use Modules\HRMS\app\Http\Traits\ApprovingListTrait;
use Modules\HRMS\app\Models\RecruitmentScript;

class PendingApproveHandler implements StatusHandlerInterface
{

    use ApprovingListTrait;

    private RecruitmentScript $script;

    public function __construct(RecruitmentScript $script)
    {
        $this->script = $script;
    }

    public function execute(): void
    {
        $script = $this->script;
        \DB::transaction(function () use ($script) {
            $this->approvingStore($script);
        });
    }
}
