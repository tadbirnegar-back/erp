<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Modules\HRMS\app\Models\Resume;

trait ResumeTrait
{
    public function resumeStore(array|Collection $data, int $workForceID)
    {
        $dataToInsert = $this->resumeDataPreparation($data, $workForceID);


        $resume = Resume::insert($dataToInsert);

        $resumes = Resume::orderBy('id', 'desc')
            ->take(count($dataToInsert))
            ->get();

        return $resumes;

    }

    public function resumeUpdate(array $data, Resume $resume)
    {
        $resume->company_name = $data['companyName'];
        $resume->start_date = $data['startDate'];
        $resume->end_date = $data['endDate'] ?? null;
        $resume->position = $data['position'];
        $resume->salary = $data['salary'] ?? null;
        $resume->work_force_id = $data['workForceId'];
        $resume->city = $data['city'];

        $resume->save();
        return $resume;
    }

    public function resumeBulkUpdate(array|Collection $data, int $workForceID)
    {
        $dataToInsert = $this->resumeDataPreparation($data, $workForceID);
    }

    private function resumeDataPreparation(array|Collection $resumes, int $workForceID)
    {
        if (is_array($resumes)) {
            $resumes = collect($resumes);
        }

        $resumes = $resumes->map(fn($data) => [
            'id' => $data['id'],
            'company_name' => $data['companyName'],
            'start_date' => $data['startDate'],
            'end_date' => $data['endDate'] ?? null,
            'position' => $data['position'],
            'salary' => $data['salary'] ?? null,
            'work_force_id' => $workForceID,
        ],
        );

        return $resumes;
    }
}
