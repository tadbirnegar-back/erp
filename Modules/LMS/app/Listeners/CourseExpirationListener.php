<?php

namespace Modules\LMS\app\Listeners;

use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\LMS\app\Jobs\CourseExpirationJob;
use Modules\LMS\app\Models\Course;

class CourseExpirationListener
{
    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        if ($event->course->expiration_date !== null) {
            $date = convertPersianToGregorianBothHaveTimeAndDont($event->course->expiration_date);
            $expirationDate = Carbon::parse($date);
            CourseExpirationJob::dispatch($event->course->id)->delay($expirationDate);
        }
    }
}
