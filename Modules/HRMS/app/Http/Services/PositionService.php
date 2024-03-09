<?php

namespace Modules\HRMS\app\Http\Services;

use Modules\HRMS\app\Http\Repositories\PositionRepository;

class PositionService
{
    protected PositionRepository $positionRepository;

    public function __construct(PositionRepository $positionRepository)
    {
        $this->positionRepository = $positionRepository;
    }

    public function index()
    {
        $result = $this->positionRepository->index();

        return $result;
    }

    public function store(array $data)
    {
        return $this->positionRepository->store($data);
    }

    public function update(array $data,int $ID)
    {
        return $this->positionRepository->update($data,$ID);

    }

    public function show(int $id)
    {
        return $this->positionRepository->show($id);
    }
}
