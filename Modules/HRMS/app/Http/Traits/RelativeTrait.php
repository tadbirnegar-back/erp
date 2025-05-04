<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Modules\HRMS\app\Models\Relative;

trait RelativeTrait
{
    public function relativeStore(array $dataToInsert, int $workForceID)
    {
        if (!isset($dataToInsert[0]) || !is_array($dataToInsert[0])) {
            $dataToInsert = [$dataToInsert];
        }
        $dataToInsert = $this->relativeDataPreparation($dataToInsert, $workForceID);


        /** @var Relative $relative */

        $relativesInsertion = Relative::insert($dataToInsert->toArray());

        $records = Relative::orderBy('id', 'desc')->take(count($dataToInsert))->get();

        return $records;

    }

    public function relativeUpdate(array $data, Relative $relative)
    {


        $relative->full_name = $data['fullName'];
        $relative->birthdate = $data['birthdate'] ?? null;
        $relative->mobile = $data['mobile'] ?? null;
        $relative->level_of_educational_id = $data['levelOfEducationalID'] ?? null;
        $relative->relative_type_id = $data['relativeTypeID'] ?? null;
        $relative->work_force_id = $data['workForceID'];
        $relative->person_id = $data['personID'] ?? null;


        $relative->save();

        return $relative;
    }

    public function relativeBulkUpdate(array|Collection $relatives, int $workForceID)
    {
        $dataToUpsert = $this->relativeDataPreparation($relatives, $workForceID);
        $rels = Relative::Upsert($dataToUpsert, ['id']);
        return $rels;
    }

    private function relativeDataPreparation(array|Collection $relatives, ?int $workForceID)
    {
        if (is_array($relatives)) {
            $relatives = collect($relatives);
        }
        $relatives = $relatives->map(fn($data) => [
            'id' => $data['id'] ?? null,
            'full_name' => $data['fullName'],
            'birthdate' => $data['birthdate'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'level_of_educational_id' => $data['levelOfEducationalID'] ?? null,
            'relative_type_id' => $data['relativeTypeID'] ?? null,
            'work_force_id' => $workForceID ?? null,
            'person_id' => $data['personID'] ?? null,

        ]);


        return $relatives;
    }
}
