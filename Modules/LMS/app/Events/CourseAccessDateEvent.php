<?php

namespace Modules\LMS\app\Events;

use Illuminate\Queue\SerializesModels;
use Modules\LMS\app\Models\Course;

class CourseAccessDateEvent
{
    use SerializesModels;

    public Course $course;

    /**
     * Create a new event instance.
     */
    public function __construct(Course $course)
    {
        $this -> course  = $course;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
