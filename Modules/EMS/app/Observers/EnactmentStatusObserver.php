<?php

namespace Modules\EMS\app\Observers;

use Modules\EMS\app\Models\EnactmentStatus;

class EnactmentStatusObserver
{
    /**
     * Handle the EnactmentStatusObserver "created" event.
     */
    public function created(EnactmentStatus $encStatus): void
    {
//        event(new EnactmentStatusCreatedEvent($encStatus));
    }

    /**
     * Handle the EnactmentStatusObserver "updated" event.
     */
    public function updated(EnactmentStatus $encStatus): void
    {
        //
    }

    /**
     * Handle the EnactmentStatusObserver "deleted" event.
     */
    public function deleted(EnactmentStatus $encStatus): void
    {
        //
    }

    /**
     * Handle the EnactmentStatusObserver "restored" event.
     */
    public function restored(EnactmentStatus $encStatus): void
    {
        //
    }

    /**
     * Handle the EnactmentStatusObserver "force deleted" event.
     */
    public function forceDeleted(EnactmentStatus $encStatus): void
    {
        //
    }
}
