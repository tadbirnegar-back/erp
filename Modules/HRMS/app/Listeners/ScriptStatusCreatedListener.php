<?php

namespace Modules\HRMS\app\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\HRMS\app\Events\ScriptStatusCreatedEvent;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;

class ScriptStatusCreatedListener
{
    use RecruitmentScriptTrait;

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
        if ($recstatus->status_id == $this->pendingRsStatus()->id) {
            Log::info($recstatus);
        }

    }
}
