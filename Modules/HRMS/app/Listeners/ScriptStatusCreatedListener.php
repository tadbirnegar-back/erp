<?php

namespace Modules\HRMS\app\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\HRMS\app\Events\ScriptStatusCreatedEvent;

class ScriptStatusCreatedListener
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
    public function handle(ScriptStatusCreatedEvent $event): void
    {
        $recstatus = $event->recStatus;

        Log::info($recstatus);
    }
}
