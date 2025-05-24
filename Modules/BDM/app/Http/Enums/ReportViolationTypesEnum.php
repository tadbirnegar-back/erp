<?php

namespace Modules\BDM\app\Http\Enums;

enum ReportViolationTypesEnum: int
{
    case violation = 1;
    case warning = 2;

    public function name(): string
    {
        return match ($this) {
            self::violation => 'گزارش وقوع تخلف',
            self::warning => 'گزارش عدم وقوع تخلف و اتمام مرحله',
        };
    }
}
