<?php

namespace Modules\EVAL\app\Http\Enums;

enum EvaluationStatusEnum: string
{
    case WAIT_TO_DONE = 'در انتظار تکمیل';
    case DONE = 'تکمیل شده';
    case EXPIRED = 'منقضی شده';
}
