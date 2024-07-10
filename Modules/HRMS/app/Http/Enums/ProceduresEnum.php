<?php

namespace Modules\HRMS\app\Http\Enums;

use Modules\HRMS\app\Http\Services\HierarchyService;
use Modules\HRMS\app\Http\Services\ManagerService;
use Modules\HRMS\app\Models\Employee;
use Modules\OUnitMS\app\Models\OrganizationUnit;

enum ProceduresEnum: int
{
    case hierarchy = 1;
    case manager = 2;
    case headUnit = 3;
    case specificEmployee = 4;

    public function getLabel()
    {
        return match ($this) {
            self::hierarchy => 'سلسله مراتبی',
            self::manager => 'مدیر مستقیم',
            self::headUnit => 'مدیر واحد خاص',
            self::specificEmployee => 'شخص خاص',
        };
    }

    public function getLabelAndValue()
    {
        return [
            'label' => $this->getLabel(),
            'value' => $this->value
        ];
    }

    public function getOptionType()
    {
        return match ($this) {
            self::hierarchy=>HierarchyService::class,
            self::manager => ManagerService::class,
            self::headUnit => OrganizationUnit::class,
            self::specificEmployee => Employee::class,
        };
    }

    public static function proceduresList()
    {
        $procedures = collect(self::cases());

        $result = $procedures->map(fn($item, $key) => [
            'value' => $item->value,
            'label' => $item->getLabel(),
        ]);

        return $result;
    }


}
