<?php

namespace Modules\EMS\app\Observers;

use Illuminate\Support\Facades\Log;
use Modules\EMS\app\Models\Meeting;

class MeetingDateObserver
{
    /**
     * Handle the MeetingDateObserver "created" event.
     */
    public function created(Meeting $meeting): void
    {
        Log::info($meeting);
    }

    /**
     * Handle the MeetingDateObserver "updated" event.
     */
    public function updated(Meeting $meeting): void
    {
        //
    }

    /**
     * Handle the MeetingDateObserver "deleted" event.
     */
    public function deleted(Meeting $meeting): void
    {
        //
    }

    /**
     * Handle the MeetingDateObserver "restored" event.
     */
    public function restored(Meeting $meeting): void
    {
        //
    }

    /**
     * Handle the MeetingDateObserver "force deleted" event.
     */
    public function forceDeleted(Meeting $meeting): void
    {
        //
    }
}
