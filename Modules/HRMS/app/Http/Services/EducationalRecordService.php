<?php

namespace Modules\HRMS\app\Http\Services;

use Modules\HRMS\app\Http\Repositories\EducationalRecordRepository;

class EducationalRecordService
{
    protected EducationalRecordRepository $educationalRecordRepository;

    /**
     * @param EducationalRecordRepository $educationalRecordRepository
     */
    public function __construct(EducationalRecordRepository $educationalRecordRepository)
    {
        $this->educationalRecordRepository = $educationalRecordRepository;
    }

    public function store(array $data)
    {
        return $this->educationalRecordRepository->store($data);
    }
    public function update(array $data,int $id)
    {
        return $this->educationalRecordRepository->update($data, $id);
    }

}
