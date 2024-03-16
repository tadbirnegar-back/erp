<?php

namespace Modules\HRMS\app\Http\Repositories;

use Modules\HRMS\app\Models\Relative;

class RelativeRepository
{
//    protected Relative $relative;
//
//    /**
//     * @param Relative $relative
//     */
//    public function __construct(Relative $relative)
//    {
//        $this->relative = $relative;
//    }

    public function store(string $json,int $workForceID)
    {

        $dataToInsert = $this->dataPreparation($json, $workForceID);

        try {

            \DB::beginTransaction();
            /** @var Relative $relative */

            $relativesInsertion = Relative::insert($dataToInsert);
//            $relative = new Relative();
//
//            $relative->full_name = $data['fullName'];
//            $relative->birthdate = $data['birthdate'] ?? null;
//            $relative->mobile = $data['mobile'];
//            $relative->level_of_educational_id = $data['levelOfEducationalId'] ?? null;
//            $relative->relative_type_id = $data['relativeTypeId'] ?? null;
//            $relative->work_force_id = $data['workForceId'];
//
//            $relative->save();
            \DB::commit();
           $records = Relative::orderBy('id', 'desc')->take(count($dataToInsert))->get();

            return $records;
        } catch (\Exception $e) {
            \DB::rollBack();

            return $e;

        }
    }

    public function update(array $data, int $id)
    {
        try {

            \DB::beginTransaction();
            /** @var Relative $relative */
            $relative = Relative::findOrFail($id);

            $relative->full_name = $data['fullName'];
            $relative->birthdate = $data['birthdate'] ?? null;
            $relative->mobile = $data['mobile'];
            $relative->level_of_educational_id = $data['levelOfEducationalID'] ?? null;
            $relative->relative_type_id = $data['relativeTypeID'] ?? null;
            $relative->work_force_id = $data['workForceID'];

            $relative->save();
            \DB::commit();
            return $relative;

        } catch (\Exception $e) {
            \DB::rollBack();

            return $e;

        }
    }

    private function dataPreparation(string $json, int $workForceID)
    {
        $relativesArray = json_decode($json, true);

        $relatives = array_map(function ($data) use ($workForceID) {
            return [
                'full_name' => $data['fullName'],
                'birthdate' => $data['birthdate'] ?? null,
                'mobile' => $data['mobile'],
                'level_of_educational_id' => $data['levelOfEducationalId'] ?? null,
                'relative_type_id' => $data['relativeTypeId'] ?? null,
                'work_force_id' => $workForceID,
            ];
        }, $relativesArray);


        return $relatives;
    }

}
