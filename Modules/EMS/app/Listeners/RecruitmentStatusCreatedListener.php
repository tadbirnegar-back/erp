<?php

namespace Modules\EMS\app\Listeners;

use Modules\EMS\app\Events\RecruitmentStatusCreatedEvent;

class RecruitmentStatusCreatedListener
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
    public function handle(RecruitmentStatusCreatedEvent $event): void
    {

    }

}
