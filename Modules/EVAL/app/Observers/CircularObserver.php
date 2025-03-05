<?php

namespace Modules\EVAL\app\Observers;

use Modules\EVAL\app\Events\CircularExpirationEvent;
use Modules\EVAL\app\Models\EvalCircular;

class CircularObserver
{
    /**
     * Handle the CircularObserver "created" event.
     */
    public function created(EvalCircular $circular): void
    {
        event(new CircularExpirationEvent($circular));
    }

    /**
     * Handle the CircularObserver "updated" event.
     */
    public function updated(EvalCircular $circular): void
    {
        event(new CircularExpirationEvent($circular));

    }

    /**
     * Handle the CircularObserver "deleted" event.
     */
    public function deleted(CircularObserver $circularObserver): void
    {
        //
    }

    /**
     * Handle the CircularObserver "restored" event.
     */
    public function restored(CircularObserver $circularObserver): void
    {
        //
    }

    /**
     * Handle the CircularObserver "force deleted" event.
     */
    public function forceDeleted(CircularObserver $circularObserver): void
    {
        //
    }
}
