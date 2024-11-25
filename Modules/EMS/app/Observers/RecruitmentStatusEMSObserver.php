<?php

namespace Modules\EMS\app\Observers;

use Modules\EMS\app\Events\RecruitmentStatusCreatedEvent;
use Modules\HRMS\app\Models\RecruitmentScriptStatus;

class RecruitmentStatusEMSObserver
{
    /**
     * Handle the RecruitmentStatus "created" event.
     */
    public function created(RecruitmentScriptStatus $recruitmentstatus): void
    {
        event(new RecruitmentStatusCreatedEvent($recruitmentstatus));
    }

    /**
     * Handle the RecruitmentStatus "updated" event.
     */
    public function updated(RecruitmentScriptStatus $recruitmentstatus): void
    {
        //
    }

    /**
     * Handle the RecruitmentStatus "deleted" event.
     */
    public function deleted(RecruitmentScriptStatus $recruitmentstatus): void
    {
        //
    }

    /**
     * Handle the RecruitmentStatus "restored" event.
     */
    public function restored(RecruitmentScriptStatus $recruitmentstatus): void
    {
        //
    }

    /**
     * Handle the RecruitmentStatus "force deleted" event.
     */
    public function forceDeleted(RecruitmentScriptStatus $recruitmentstatus): void
    {
        //
    }
}
