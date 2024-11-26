<?php

namespace Modules\EMS\app\Listeners;

use Modules\EMS\app\Events\RecruitmentStatusCreatedEvent;
use Modules\EMS\app\Jobs\RecruitmentStatusCreatedJob;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Http\Traits\ScriptTypeTrait;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptType;

class RecruitmentStatusCreatedListener
{
    use RecruitmentScriptTrait, ScriptTypeTrait;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RecruitmentStatusCreatedEvent $event): void
    {
        $recstatus = $event->recStatus;
        $ApprovalStatus = $this->activeRsStatus()->id;
        if ($recstatus->status_id == $ApprovalStatus) {
            $recruitmentScript = RecruitmentScript::find($recstatus->recruitment_script_id);

            $scriptTypeId = ScriptType::where('title', 'انتصاب هیئت تطبیق')->first()->id;

            if ($recruitmentScript->script_type_id == $scriptTypeId) {
                RecruitmentStatusCreatedJob::dispatch($recruitmentScript);
            }
        }
    }
}
