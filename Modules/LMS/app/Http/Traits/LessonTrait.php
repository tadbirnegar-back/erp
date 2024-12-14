<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Models\Lesson;

class LessonTrait
{
    public function lessonActiveStatus()
    {
        return Lesson::GetAllStatuses()->firstWhere('name', LessonStatusEnum::ACTIVE->value);
    }
    public function lessonInActiveStatus()
    {
        return Lesson::GetAllStatuses()->firstWhere('name', LessonStatusEnum::IN_ACTIVE->value);
    }
}
