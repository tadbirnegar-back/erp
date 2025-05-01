<?php

namespace Modules\BDM\app\Http\Enums;

enum TransferTypesEnum: string
{
    case NORMAL = 'بنیاد مسکن / عادی';
    case NINETY_NINE = 'بنیاد مسکن / ۹۹ ساله';
    case OTHERS = 'سایر';

    public function id(): int
    {
        return match ($this) {
            self::NORMAL => 1,
            self::NINETY_NINE => 2,
            self::OTHERS => 3,
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
