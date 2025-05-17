<?php

namespace Modules\ODOC\app\Observers;

use Modules\EMS\app\Events\EnactmentMeetingEvent;
use Modules\ODOC\app\Events\OdocApproversEvent;
use Modules\ODOC\app\Models\Approvers;

class OdocApproversObserver
{
    /**
     * Handle the OdocApprovers "created" event.
     */
    public function created(Approvers $odocapprovers): void
    {
    }

    /**
     * Handle the OdocApprovers "updated" event.
     */
    public function updated(Approvers $odocapprovers): void
    {
        event(new OdocApproversEvent($odocapprovers));
    }

    /**
     * Handle the OdocApprovers "deleted" event.
     */
    public function deleted(Approvers $odocapprovers): void
    {
        //
    }

    /**
     * Handle the OdocApprovers "restored" event.
     */
    public function restored(Approvers $odocapprovers): void
    {
        //
    }

    /**
     * Handle the OdocApprovers "force deleted" event.
     */
    public function forceDeleted(Approvers $odocapprovers): void
    {
        //
    }
}
