<?php

namespace Modules\PFM\app\Http\Enums;

enum BookletStatusEnum: string
{
    case MOSAVAB = 'تصویب شده';
    case DAR_ENTEZAR_SABTE_MAGHADIR = 'در انتظار ثبت مقادیر';
    case RAD_SHODE = 'رد شده';
    case DAR_ENTEZAR_SHURA = 'در انتظار تایید شورا روستا';
    case DAR_ENTEZARE_HEYATE_TATBIGH = 'در انتظار هیئت تطبیق';

    /**
     * Get the timeline for declined statuses.
     */
    public static function getTimeLineDeclined(string $statusName): array
    {
        $timeline = [
            self::DAR_ENTEZAR_SABTE_MAGHADIR->value => ['danger', 'secondary', 'secondary'],
            self::DAR_ENTEZAR_SHURA->value => ['success', 'danger', 'secondary'],
            self::DAR_ENTEZARE_HEYATE_TATBIGH->value => ['success', 'success', 'danger'],
        ];

        return $timeline[$statusName] ?? [];
    }
}
