<?php

namespace Modules\HRMS\app\Http\Repositories;

use Modules\HRMS\app\Models\Resume;
use mysql_xdevapi\Exception;

class ResumeRepository
{
    protected Resume $resume;

    /**
     * @param Resume $resume
     */
    public function __construct(Resume $resume)
    {
        $this->resume = $resume;
    }

    public function store(array $data)
    {

        try {

            \DB::beginTransaction();
            /** @var Resume $resume */
            $resume = new $this->resume();

            $resume->company_name = $data['companyName'];
            $resume->start_date = $data['startDate'];
            $resume->end_date = $data['endDate'] ?? null;
            $resume->position = $data['position'];
            $resume->salary = $data['salary'] ?? null;
            $resume->work_force_id = $data['workForceId'];

            $resume->save();
            \DB::commit();
            return $resume;
        } catch (\Exception $e) {
            \DB::rollBack();

            return $e;

        }
    }

}
