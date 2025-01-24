<?php

namespace Modules\LMS\app\Events;

use Modules\LMS\app\Models\Student;

class StudentRoleCreatedEvent
{
    public Student $student;

    public function __construct($student)
    {
        $this->student = $student;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
