<?php

namespace Modules\EMS\app\Observers;

use Modules\EMS\app\Events\EnactmentMeetingEvent;
use Modules\EMS\app\Models\EnactmentMeeting;

class EnactmentMeetingObserver
{
    /**
     * Handle the EnactmentMeeting "created" event.
     */
    public function created(EnactmentMeeting $enactmentmeeting): void
    {
        //
    }

    /**
     * Handle the EnactmentMeeting "updated" event.
     */
    public function updated(EnactmentMeeting $enactmentmeeting): void
    {
        //
    }

    /**
     * Handle the EnactmentMeeting "deleted" event.
     */
    public function deleted(EnactmentMeeting $enactmentmeeting): void
    {
        event(new EnactmentMeetingEvent($enactmentmeeting));
    }

    /**
     * Handle the EnactmentMeeting "restored" event.
     */
    public function restored(EnactmentMeeting $enactmentmeeting): void
    {
        //
    }

    /**
     * Handle the EnactmentMeeting "force deleted" event.
     */
    public function forceDeleted(EnactmentMeeting $enactmentmeeting): void
    {
        //
    }
}
