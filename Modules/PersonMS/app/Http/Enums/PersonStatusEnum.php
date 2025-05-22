<?php

namespace Modules\PersonMS\app\Http\Enums;

enum PersonStatusEnum: string
{
    case CASE_CREATED = 'تشکیل پرونده';
    case PENDING_TO_FILL = 'در انتظار تکمیل';
    case PENDING_TO_APPROVE = 'در انتظار تایید';
    case CONFIRMED = 'تایید شده';
    case UPDATED = 'بروزرسانی شده';


    public function getClassName(): string
    {
        return match ($this) {
            self::CASE_CREATED => 'primary',
            self::PENDING_TO_FILL => 'warning',
            self::PENDING_TO_APPROVE => 'primary',
            self::CONFIRMED => 'success',
            self::UPDATED => 'primary',
        };
    }
}
