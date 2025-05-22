<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Http\Enums\DependentStatusEnum;
use Modules\HRMS\app\Http\Enums\RelationTypeEnum;
use Modules\HRMS\app\Models\Dependent;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;

trait DependentTrait
{

    public function storeDependent(array $data, Person $mainPerson)
    {
        $status = $this->pendingDependentStatus();
        $preparedData = $this->dependentDataPreparation($data, $mainPerson, $status);

        $dependent = Dependent::create($preparedData->toArray()[0]);

        return $dependent;


    }

    public function pendingDependentStatus()
    {
        return Dependent::GetAllStatuses()->firstWhere('name', '=', DependentStatusEnum::PENDING->value);
    }

    public function approvedDependentStatus()
    {
        return Dependent::GetAllStatuses()->firstWhere('name', '=', DependentStatusEnum::ACTIVE->value);
    }

    public function dependentDataPreparation(array $data, Person $mainPerson, Status $status)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }


        $data = collect($data)->map(function ($item) use ($mainPerson, $status) {

            return [
                'main_person_id' => $mainPerson->id,
                'related_person_id' => $item['relatedPersonID'],
                'relation_type_id' => RelationTypeEnum::CHILD->value,
                'status_id' => $status->id,
            ];
        });

        return $data;
    }
}
