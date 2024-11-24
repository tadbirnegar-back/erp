<?php

namespace Modules\HRMS\app\Observers;

use Modules\HRMS\app\Models\RecruitmentScriptStatus;

class ScriptStatusObserver
{
    /**
     * Handle the ScriptStatusObserver "created" event.
     */
    public function created(RecruitmentScriptStatus $scriptStatus): void
    {
//        event(new ScriptStatusCreatedEvent($scriptStatus));
    }

    /**
     * Handle the ScriptStatusObserver "updated" event.
     */
    public function updated(RecruitmentScriptStatus $scriptstatusobserver): void
    {
        //
    }

    /**
     * Handle the ScriptStatusObserver "deleted" event.
     */
    public function deleted(RecruitmentScriptStatus $scriptstatusobserver): void
    {
        //
    }

    /**
     * Handle the ScriptStatusObserver "restored" event.
     */
    public function restored(RecruitmentScriptStatus $scriptstatusobserver): void
    {
        //
    }

    /**
     * Handle the ScriptStatusObserver "force deleted" event.
     */
    public function forceDeleted(RecruitmentScriptStatus $scriptstatusobserver): void
    {
        //
    }
}
