<?php

namespace Modules\ACMS\app\Http\Trait;

use Modules\ACMS\app\Models\CircularSubject;

trait CircularSubjectsTrait
{
    public function storeSubject(array $data)
    {
        $preparedData = $this->circularSubjectsDataPreparation($data);

        $subject = CircularSubject::create($preparedData->toArray()[0]);

        return $subject;
    }

    public function updateSubject(array $data, CircularSubject $circularSubject)
    {
        $preparedData = $this->circularSubjectsDataPreparation($data);
        $circularSubject->update($preparedData->toArray()[0]);
    }

    public function circularSubjectsDataPreparation(array $data)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $data = collect($data)->map(function ($item) {

            return [
                'name' => convertToDbFriendly($item['subjectName']),
                'isActive' => $item['isActive'] ?? true,
                'code' => $item['code'] ?? null,
                'old_item_id' => $item['oldItemID'] ?? null,
                'parent_id' => $item['parentID'] ?? null,
                'subject_type_id' => $item['subjectTypeID'],
                'create_date' => $item['create_date'] ?? now(),
            ];
        });

        return $data;

    }

}

