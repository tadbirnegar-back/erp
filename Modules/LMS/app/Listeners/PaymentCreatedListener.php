<?php

namespace Modules\LMS\app\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\LMS\app\Jobs\PaymentVerificationJob;

class PaymentCreatedListener
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
        $payment = $event->payment;

        PaymentVerificationJob::dispatch($payment)->delay(now()->addMinutes(15));
    }
}
