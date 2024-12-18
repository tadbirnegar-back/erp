<?php

namespace Modules\LMS\app\Observers;

use Illuminate\Support\Facades\Log;
use Modules\LMS\app\Events\PaymentCreatedEvent;
use Modules\PayStream\app\Models\PsPayments;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(PsPayments $payment): void
    {
        event(new PaymentCreatedEvent($payment));
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(PsPayments $payment): void
    {
        //
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(PsPayments $payment): void
    {
        //
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(PsPayments $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(PsPayments $payment): void
    {
        //
    }
}
