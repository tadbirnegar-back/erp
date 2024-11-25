<?php

namespace Modules\HRMS\app\Observers;

use Modules\HRMS\app\Events\ScriptCreatedEvent;
use Modules\HRMS\app\Models\RecruitmentScript;

class ScriptObserver
{
    /**
     * Handle the Script "created" event.
     */
    public function created(RecruitmentScript $script): void
    {
        event(new ScriptCreatedEvent($script));

    }

    /**
     * Handle the Script "updated" event.
     */
    public function updated(RecruitmentScript $script): void
    {
        //
    }

    /**
     * Handle the Script "deleted" event.
     */
    public function deleted(RecruitmentScript $script): void
    {
        //
    }

    /**
     * Handle the Script "restored" event.
     */
    public function restored(RecruitmentScript $script): void
    {
        //
    }

    /**
     * Handle the Script "force deleted" event.
     */
    public function forceDeleted(RecruitmentScript $script): void
    {
        //
    }
}
