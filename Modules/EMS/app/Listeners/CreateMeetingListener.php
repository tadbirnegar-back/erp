<?php

namespace Modules\EMS\app\Listeners;

use Illuminate\Support\Facades\Log;

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
        Log::info($event);
    }
}
