<?php
namespace Modules\EVAL\app\Http\Enums;
enum EvalCircularStatusEnum: string
{
    case DELETED = 'حذف شده';
    case NOTIFIED = 'ابلاغ شده';
    case COMPLETED = 'تکمیل شده';
    case PISHNEVIS = 'پیش نویس';
    case WAITING = 'در انتظار تکمیل';
    case EXPIRED = 'منقضی شده';
}
