<?php

namespace Modules\BDM\app\Http\Enums;

enum DossierStatusesEnum : string
{
    case WAIT_TO_DONE = 'در حال تکمیل';
    case DONE = 'تکمیل شده';
    case EXPIRED = 'منقضی شده';
    case ARCHIVE = "بایگانی";

    public function id(): int
    {
        return match ($this) {
            self::WAIT_TO_DONE => 1,
            self::DONE => 2,
            self::EXPIRED => 3,
        };
    }

    public static function listWithIds(): array
    {
        return array_map(fn($case) => [
            'id' => $case->id(),
            'name' => $case->value,
        ], self::cases());
    }

    public static function fromId(int $id): ?self
    {
        return match ($id) {
            1 => self::WAIT_TO_DONE,
            2 => self::DONE,
            3 => self::EXPIRED,
            default => null,
        };
    }

}
