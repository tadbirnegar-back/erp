<?php

namespace Modules\HRMS\app\Http\Services;

use Modules\HRMS\app\Http\Repositories\SkillRepository;

class SkillService
{
    protected SkillRepository $skillRepository;

    public function __construct(SkillRepository $skillRepository)
    {
        $this->skillRepository = $skillRepository;
    }

    public function index()
    {
        return $this->skillRepository->index();
    }

    public function store(array $data)
    {
        return $this->skillRepository->store($data);
    }

    public function show(int $id)
    {
        return $this->skillRepository->show($id);
    }

    public function update(array $data, int $id)
    {
        return $this->skillRepository->update($data, $id);
    }
}
