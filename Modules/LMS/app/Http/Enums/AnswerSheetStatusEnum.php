<?php

namespace Modules\LMS\app\Http\Enums;

enum AnswerSheetStatusEnum: string
{
    case TAKING_EXAM = "در حال آزمون";
    case APPROVED = "قبول شده";
    case DECLINED = "رد شده";
}
