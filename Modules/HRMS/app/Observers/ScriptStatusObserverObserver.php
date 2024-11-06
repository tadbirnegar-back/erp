<?php

namespace Modules\HRMS\app\Observers;

use Modules\HRMS\app\Events\ScriptStatusCreatedEvent;
use Modules\HRMS\app\Models\recruitmentScriptStatus;

class ScriptStatusObserverObserver
{
    /**
     * Handle the ScriptStatusObserver "created" event.
     */
    public function created(recruitmentScriptStatus $scriptStatus): void
    {
        event(new ScriptStatusCreatedEvent($scriptStatus));
    }

    /**
     * Handle the ScriptStatusObserver "updated" event.
     */
    public function updated(recruitmentScriptStatus $scriptstatusobserver): void
    {
        //
    }

    /**
     * Handle the ScriptStatusObserver "deleted" event.
     */
    public function deleted(recruitmentScriptStatus $scriptstatusobserver): void
    {
        //
    }

    /**
     * Handle the ScriptStatusObserver "restored" event.
     */
    public function restored(recruitmentScriptStatus $scriptstatusobserver): void
    {
        //
    }

    /**
     * Handle the ScriptStatusObserver "force deleted" event.
     */
    public function forceDeleted(recruitmentScriptStatus $scriptstatusobserver): void
    {
        //
    }
}
