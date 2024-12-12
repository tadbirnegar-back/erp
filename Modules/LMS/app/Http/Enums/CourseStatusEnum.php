<?php

namespace Modules\LMS\app\Http\Enums;

enum CourseStatusEnum: string
{
    case PRESENTING = "در حال برگزاری";
    case ENDED = "به پایان رسیده";
    case CANCELED = "لغو شده";


}
