<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\HRMS\app\Models\HireType;

trait HireTypeTrait
{
    private string $activeHireName = 'فعال';
    private string $inactiveHireName = 'غیرفعال';

    public function createHireType(array $data): HireType
    {
        $hireType = new HireType();
        $hireType->title = $data['title'];
        $hireType->work_hour = $data['workHour'];
        $hireType->contract_type_id = $data['contractTypeID'];
        $hireType->status_id = $this->activeHireTypeStatus()->id;
        $hireType->save();
        $hireType->load('contractType');


        return $hireType;
    }

    public function getHireType(int $id): ?HireType
    {
        return HireType::find($id);
    }

    public function getAllHireTypes()
    {
        return HireType::whereHas('status', function ($query) {
            $query->where('name', '=', $this->activeHireName);
        })->with('contractType')->get();
    }

    public function updateHireType(HireType $hireType, array $data): HireType
    {
        $hireType->title = $data['title'] ?? $hireType->title;
        $hireType->work_hour = $data['workHour'] ?? $hireType->work_hour;
        $hireType->contract_type_id = $data['contractTypeID'] ?? $hireType->contract_type_id;

        $hireType->save();
        $hireType->load('contractType');
        return $hireType;
    }

    public function deleteHireType(HireType $hireType)
    {
        $hireType->status_id = $this->inactiveHireTypeStatus()->id;
        $hireType->save();
        return $hireType;
    }

    public function activeHireTypeStatus()
    {
        return Cache::rememberForever('hire_type_active_status', function () {
            return HireType::GetAllStatuses()
                ->firstWhere('name', '=', $this->activeHireName);
        });
    }

    public function inactiveHireTypeStatus()
    {
        return Cache::rememberForever('hire_type_inactive_status', function () {
            return HireType::GetAllStatuses()
                ->firstWhere('name', '=', $this->inactiveHireName);
        });
    }
}
