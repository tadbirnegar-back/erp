<?php

namespace Modules\SMM\app\Enums;

enum CircularStatusEnum: string
{

    case DRAFT = 'در انتظار ابلاغ';
    case DISPATCHED = 'ابلاغ شده';
    case EXPIRED = 'منقضی شده';
    case CANCELED = 'لغو شده';

    public function getClassName()
    {
        return match ($this) {
            self::DRAFT => 'primary',
            self::DISPATCHED => 'success',
            self::EXPIRED, self::CANCELED => 'danger',
        };
    }
}
