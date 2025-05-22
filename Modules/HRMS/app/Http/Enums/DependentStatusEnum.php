<?php

namespace Modules\HRMS\app\Http\Enums;

enum DependentStatusEnum: string
{
    case ACTIVE = 'تایید شده';
    case PENDING = 'در انتظار تایید';
    case REJECTED = 'رد شده';

    public function getClassName(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::PENDING => 'warning',
            self::REJECTED => 'danger',
        };
    }
}
