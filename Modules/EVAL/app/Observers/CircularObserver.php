<?php

namespace Modules\EVAL\app\Observers;

use Modules\EVAL\app\Models\CircularObserver;

class CircularObserver
{
    /**
     * Handle the CircularObserver "created" event.
     */
    public function created(CircularObserver $circularobserver): void
    {
        //
    }

    /**
     * Handle the CircularObserver "updated" event.
     */
    public function updated(CircularObserver $circularobserver): void
    {
        //
    }

    /**
     * Handle the CircularObserver "deleted" event.
     */
    public function deleted(CircularObserver $circularobserver): void
    {
        //
    }

    /**
     * Handle the CircularObserver "restored" event.
     */
    public function restored(CircularObserver $circularobserver): void
    {
        //
    }

    /**
     * Handle the CircularObserver "force deleted" event.
     */
    public function forceDeleted(CircularObserver $circularobserver): void
    {
        //
    }
}
