<?php

namespace Modules\LMS\app\Observers;

use Modules\LMS\app\Events\CourseAccessDateEvent;
use Modules\LMS\app\Events\CourseExpirationEvent;
use Modules\LMS\app\Events\CourseExpirationUpdateEvent;
use Modules\LMS\app\Models\Course;

class CourseObserver
{
    /**
     * Handle the CourseObserver "created" event.
     */
    public function created(Course $course): void
    {
        event(new CourseExpirationEvent($course));
        event(new CourseAccessDateEvent($course));
    }

    /**
     * Handle the CourseObserver "updated" event.
     */
    public function updated(Course $course): void
    {
        event(new CourseExpirationEvent($course));
        event(new CourseAccessDateEvent($course));
    }

    /**
     * Handle the CourseObserver "deleted" event.
     */
    public function deleted(Course $course): void
    {
        //
    }

    /**
     * Handle the CourseObserver "restored" event.
     */
    public function restored(Course $course): void
    {
        //
    }

    /**
     * Handle the CourseObserver "force deleted" event.
     */
    public function forceDeleted(Course $course): void
    {
        //
    }
}
