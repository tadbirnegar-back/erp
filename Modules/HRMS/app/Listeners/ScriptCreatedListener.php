<?php

namespace Modules\HRMS\app\Listeners;

use Modules\HRMS\app\Events\ScriptCreatedEvent;
use Modules\HRMS\app\Jobs\PayanKhedmatJob;

class ScriptCreatedListener
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
    public function handle(ScriptCreatedEvent $event): void
    {
        $recruitmentScript = $event->rs;
        dispatch(new PayanKhedmatJob($recruitmentScript));
//        dispatch(new PayanKhedmatJob($recruitmentScript))->delay($recruitmentScript->expire_date);
    }
}
