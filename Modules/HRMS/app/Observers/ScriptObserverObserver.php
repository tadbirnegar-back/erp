<?php

namespace Modules\HRMS\app\Observers;

use Modules\HRMS\app\Models\ScriptObserver;

class ScriptObserverObserver
{
    /**
     * Handle the ScriptObserver "created" event.
     */
    public function created(ScriptObserver $scriptobserver): void
    {
        //
    }

    /**
     * Handle the ScriptObserver "updated" event.
     */
    public function updated(ScriptObserver $scriptobserver): void
    {
        //
    }

    /**
     * Handle the ScriptObserver "deleted" event.
     */
    public function deleted(ScriptObserver $scriptobserver): void
    {
        //
    }

    /**
     * Handle the ScriptObserver "restored" event.
     */
    public function restored(ScriptObserver $scriptobserver): void
    {
        //
    }

    /**
     * Handle the ScriptObserver "force deleted" event.
     */
    public function forceDeleted(ScriptObserver $scriptobserver): void
    {
        //
    }
}
