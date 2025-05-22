<?php

namespace Modules\HRMS\app\Http\Enums;

enum IsarStatusEnum: string
{
    case PENDING_APPROVE = 'در انتظار تایید';
    case PENDING_TO_FILL = 'در انتظار تکمیل';
    case APPROVED = 'تایید شده';
    case REJECTED = 'رد شده';


    public function getClassName()
    {
        return match ($this) {
            self::PENDING_APPROVE => 'warning',
            self::PENDING_TO_FILL => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
