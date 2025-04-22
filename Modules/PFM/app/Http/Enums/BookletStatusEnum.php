<?php

namespace Modules\PFM\app\Http\Enums;

enum BookletStatusEnum: string
{
    case MOSAVAB = 'تصویب شده';
    case DAR_ENTEZAR_SABTE_MAGHADIR = 'در انتظار ثبت مقادیر';
    case RAD_SHODE = 'رد شده';
    case DAR_ENTEZAR_SHURA = 'در انتظار تایید شورا روستا';
    case DAR_ENTEZARE_HEYATE_TATBIGH = 'در انتظار هیئت تطبیق';

    public static function getTimeLine(self $status): array
    {
        return match ($status) {
            self::MOSAVAB => [
                ['class' => 'success', 'name' => self::DAR_ENTEZAR_SABTE_MAGHADIR->value],
                ['class' => 'success', 'name' => self::DAR_ENTEZAR_SHURA->value],
                ['class' => 'success', 'name' => self::DAR_ENTEZARE_HEYATE_TATBIGH->value]
            ],
            self::DAR_ENTEZAR_SABTE_MAGHADIR => [
                ['class' => 'primary', 'name' => self::DAR_ENTEZAR_SABTE_MAGHADIR->value],
                ['class' => 'gray', 'name' => self::DAR_ENTEZAR_SHURA->value],
                ['class' => 'gray', 'name' => self::DAR_ENTEZARE_HEYATE_TATBIGH->value]
            ],
            self::DAR_ENTEZAR_SHURA => [
                ['class' => 'success', 'name' => self::DAR_ENTEZAR_SABTE_MAGHADIR->value],
                ['class' => 'primary', 'name' => self::DAR_ENTEZAR_SABTE_MAGHADIR->value],
                ['class' => 'gray', 'name' => self::DAR_ENTEZARE_HEYATE_TATBIGH->value]
            ],
            self::DAR_ENTEZARE_HEYATE_TATBIGH => [
                ['class' => 'success', 'name' => self::DAR_ENTEZAR_SABTE_MAGHADIR->value],
                ['class' => 'success', 'name' => self::DAR_ENTEZAR_SHURA->value],
                ['class' => 'primary', 'name' => self::DAR_ENTEZARE_HEYATE_TATBIGH->value]
            ],
            default => []
        };
    }
}
