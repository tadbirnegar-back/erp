<?php

namespace Modules\LMS\App\Http\Enums;

enum AnswerSheetStatusEnum: string
{
    case TAKING_EXAM = "در حال آزمون";
    case APPROVED = "قبول شده";
    case DECLINED = "رد شده";
}
