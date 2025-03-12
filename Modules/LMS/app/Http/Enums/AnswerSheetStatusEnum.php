<?php

namespace Modules\LMS\app\Http\Enums;

enum AnswerSheetStatusEnum: string
{
    case TAKING_EXAM = "در حال آزمون";
    case APPROVED = "قبول شده";
    case DECLINED = "رد شده";
    case WAIT_TO_EXAM = "در انتظار آزمون"; // this status is not listed in the statuses table and it is gonna show when user has not taken exam
}
