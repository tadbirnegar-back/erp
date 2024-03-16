<?php

namespace Modules\HRMS\app\Http\Services;

use Modules\HRMS\app\Http\Repositories\ResumeRepository;

class ResumeService
{
    protected ResumeRepository $resumeRepository;

    /**
     * @param ResumeRepository $resumeRepository
     */
    public function __construct(ResumeRepository $resumeRepository)
    {
        $this->resumeRepository = $resumeRepository;
    }


    public function store(array $data)
    {
        return $this->resumeRepository->store($data);
    }

    public function update(array $data,int $id)
    {
        return $this->resumeRepository->update($data, $id);
    }


}
