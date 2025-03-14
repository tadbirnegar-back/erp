<?php

namespace Modules\EVAL\app\Listeners;

use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\EVAL\app\Jobs\CircularExpirationJob;

class CircularExpirationListener
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
        if ($event->circular->expired_date !== null) {
            $date = $event->circular->expired_date;
            $expirationDate = Carbon::parse($date);
            CircularExpirationJob::dispatch($event->circular->id)->delay($expirationDate);
        }
    }
}
