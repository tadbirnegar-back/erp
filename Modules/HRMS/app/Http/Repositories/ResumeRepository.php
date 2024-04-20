<?php

namespace Modules\HRMS\app\Http\Repositories;

use Modules\HRMS\app\Models\Resume;
use mysql_xdevapi\Exception;

class ResumeRepository
{
//    protected Resume $resume;
//
//    /**
//     * @param Resume $resume
//     */
//    public function __construct(Resume $resume)
//    {
//        $this->resume = $resume;
//    }

    public function store(string $json, int $workForceID)
    {
        $dataToInsert = $this->dataPreparation($json, $workForceID);

        try {

            \DB::beginTransaction();
            /** @var Resume $resume */
            $resume = Resume::insert($dataToInsert);

//            $resume->company_name = $data['companyName'];
//            $resume->start_date = $data['startDate'];
//            $resume->end_date = $data['endDate'] ?? null;
//            $resume->position = $data['position'];
//            $resume->salary = $data['salary'] ?? null;
//            $resume->work_force_id = $data['workForceID'];
//
//            $resume->save();

            \DB::commit();
            return $resume;
        } catch (\Exception $e) {
            \DB::rollBack();

            return $e;

        }
    }

    public function update(array $data, int $id)
    {
        try {

            \DB::beginTransaction();
            /** @var Resume $resume */
            $resume = Resume::findOrFail($id);
            if (is_null($resume)) {
                return null;
            }
            $resume->company_name = $data['companyName'];
            $resume->start_date = $data['startDate'];
            $resume->end_date = $data['endDate'] ?? null;
            $resume->position = $data['position'];
            $resume->salary = $data['salary'] ?? null;
            $resume->work_force_id = $data['workForceId'];
            $resume->city = $data['city'];

            $resume->save();
            \DB::commit();
            return $resume;
        } catch (\Exception $e) {
            \DB::rollBack();

            return $e;

        }
    }

    private function dataPreparation(string $json, int $workForceID)
    {
        $resumesArray = json_decode($json, true);

        $resumes = array_map(function ($data) use ($workForceID) {
            return [
                'company_name' => $data['companyName'],
                'start_date' => $data['startDate'],
                'end_date' => $data['endDate'] ?? null,
                'position' => $data['position'],
                'salary' => $data['salary'] ?? null,
                'work_force_id' => $data['workForceID'],
            ];
        },
            $resumesArray);

        return $resumes;
    }
}
