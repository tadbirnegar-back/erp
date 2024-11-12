<?php

namespace Modules\EMS\app\Observers;


use Modules\EMS\app\Events\CreateMeetingEvent;
use Modules\EMS\app\Models\Meeting;

class MeetingObserver
{
    /**
     * Handle the MeetingObserver "created" event.
     */
    public function created(Meeting $meeting): void
    {
        CreateMeetingEvent::dispatch($meeting);
    }

    /**
     * Handle the MeetingObserver "updated" event.
     */
    public function updated(Meeting $meeting): void
    {
        //
    }

    /**
     * Handle the MeetingObserver "deleted" event.
     */
    public function deleted(Meeting $meeting): void
    {
        //
    }

    /**
     * Handle the MeetingObserver "restored" event.
     */
    public function restored(Meeting $meeting): void
    {
        //
    }

    /**
     * Handle the MeetingObserver "force deleted" event.
     */
    public function forceDeleted(Meeting $meeting): void
    {
        //
    }
}
