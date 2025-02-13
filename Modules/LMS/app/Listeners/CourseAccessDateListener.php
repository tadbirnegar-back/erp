<?php

namespace Modules\LMS\app\Listeners;

use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\LMS\app\Jobs\CourseAccessDateJob;
use Modules\LMS\app\Jobs\CourseExpirationJob;

class CourseAccessDateListener
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
        if ($event->course->access_date !== null) {
            $date = convertPersianToGregorianBothHaveTimeAndDont($event->course->access_date);
            $accessDate = Carbon::parse($date);
            CourseAccessDateJob::dispatch($event->course->id)->delay($accessDate);
        }
    }
}
