<?php

namespace Modules\LMS\app\Http\Enums;

enum CourseStatusEnum: string
{
    case PRESENTING = "در حال برگزاری";
    case ENDED = "به پایان رسیده";
    case CANCELED = "لغو شده";
    case PISHNEVIS = "پیش نویس";
    case DELETED = "حذف شده";
    case ORGANIZER = "برگزار شونده";
    case WAITING_TO_PRESENT = "در انتظار برگزاری";

}
