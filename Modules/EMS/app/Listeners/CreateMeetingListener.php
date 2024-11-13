<?php

namespace Modules\EMS\app\Listeners;

use Modules\EMS\app\Jobs\StoreMeetingJob;

class CreateMeetingListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        dispatch(new StoreMeetingJob($event->meeting))->delay(now()->addSeconds(600));
    }
}
