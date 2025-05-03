<?php

namespace Modules\BDM\app\Http\Enums;

enum PermitStatusesEnum: string
{
    case first = 'تشکیل پرونده ساخت و ساز';
    case second = 'بررسی اولیه توسط مسئول فنی';
    case third = 'تایید دهیار جهت ارسال به بنیاد مسکن';
    case fourth = 'استعلام از بنیاد مسکن';
    case fifth = 'بررسی فنی استعلام بنیاد مسکن';
    case sixth = 'بررسی مالکیت زمین توسط شورای روستا';
    case seventh = 'صدور دستور تهیه نقشه ساختمانی';

    case eighth = 'معرفی مهندسین پروژه';
    case ninth = 'تهیه و تنظیم نقشه‌های معماری و سازه';
    case tenth = 'بررسی فنی نقشه‌های ساختمانی';
    case eleventh = 'ثبت تعهدات مهندسین پروژه';
    case twelfth = 'محاسبه و صدور قبض عوارض پروانه ساختمانی';

    case thirteenth = 'پرداخت عوارض پروانه ساختمانی';

    case fourteenth = 'تأییدیه پرداخت‌ها توسط مسئول مالی';
    case fifteenth = 'تأیید نهایی و صدور پروانه';
    case sixteenth = 'اجرای فونداسیون و پی‌ریزی ساختمان';
    case seventeenth = 'اجرای اسکلت و عملیات سازه‌ای';
    case eighteenth = 'اجرای عملیات سفت‌کاری و نازک‌کاری';
    case nineteenth = 'ارائه گزارش نهایی ناظر';
    case twentieth = 'درخواست صدور گواهی پایان کار';
    case twentyfirst = 'صدور گواهی پایان کار';


    public function whichNumber(): string
    {
        return match ($this) {
            self::first => 'مرحله اول',
            self::second => 'مرحله دوم',
            self::third => 'مرحله سوم',
            self::fourth => 'مرحله چهارم',
            self::fifth => 'مرحله پنجم',
            self::sixth => 'مرحله ششم',
            self::seventh => 'مرحله هفتم',
            self::eighth => 'مرحله هشتم',
            self::ninth => 'مرحله نهم',
            self::tenth => 'مرحله دهم',
            self::eleventh => 'مرحله یازدهم',
            self::twelfth => 'مرحله دوازدهم',
            self::thirteenth => 'مرحله سیزدهم',
            self::fourteenth => 'مرحله چهاردهم',
            self::fifteenth => 'مرحله پانزدهم',
            self::sixteenth => 'مرحله شانزدهم',
            self::seventeenth => 'مرحله هفدهم',
            self::eighteenth => 'مرحله هجدهم',
            self::nineteenth => 'مرحله نوزدهم',
            self::twentieth => 'مرحله بیستم',
            self::twentyfirst => 'مرحله بیست و یکم',
        };


    }

    public function id(): int
    {
        return match ($this) {
            self::first => 1,
            self::second => 2,
            self::third => 3,
            self::fourth => 4,
            self::fifth => 5,
            self::sixth => 6,
            self::seventh => 7,
            self::eighth => 8,
            self::ninth => 9,
            self::tenth => 10,
            self::eleventh => 11,
            self::twelfth => 12,
            self::thirteenth => 13,
            self::fourteenth => 14,
            self::fifteenth => 15,
            self::sixteenth => 16,
            self::seventeenth => 17,
            self::eighteenth => 18,
            self::nineteenth => 19,
            self::twentieth => 20,
            self::twentyfirst => 21,
        };
    }
    public static function listWithIds(): array
    {
        return array_map(fn($case) => [
            'id' => $case->id(),
            'name' => $case->value,
        ], self::cases());
    }


}
