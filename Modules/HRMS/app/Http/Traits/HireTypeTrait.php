<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\HireType;

trait HireTypeTrait
{
    public function createHireType(array $data): HireType
    {
        $hireType = new HireType();
        $hireType->title = $data['title'];
        $hireType->work_hour = $data['work_hour'];
        $hireType->contract_type_id = $data['contract_type_id'];
        $hireType->save();

        return $hireType;
    }

    public function getHireType(int $id): ?HireType
    {
        return HireType::find($id);
    }

    public function getAllHireTypes()
    {
        return HireType::all();
    }

    public function updateHireType(int $id, array $data): HireType
    {
        $hireType = HireType::findOrFail($id);
        $hireType->title = $data['title'] ?? $hireType->title;
        $hireType->work_hour = $data['work_hour'] ?? $hireType->work_hour;
        $hireType->contract_type_id = $data['contract_type_id'] ?? $hireType->contract_type_id;
        $hireType->save();

        return $hireType;
    }

    public function deleteHireType(int $id): bool
    {
        $hireType = HireType::findOrFail($id);
        return $hireType->delete();
    }
}
