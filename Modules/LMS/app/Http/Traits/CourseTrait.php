<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\AAA\app\Models\User;
use Modules\LMS\app\Models\Enroll;

trait CourseTrait
{
    public function courseShow($course, $user)
    {
        $userQuery = User::query();
        $userQuery->joinRelationship('order'  , function ($join) use ($course) {
            $join->on('order.course_id', '=', 'course.id');

        });
        $order = $userQuery->first();

        return $order;

    }
}
