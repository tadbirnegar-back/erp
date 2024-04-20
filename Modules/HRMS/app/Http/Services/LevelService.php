<?php

namespace Modules\HRMS\app\Http\Services;

use Modules\HRMS\app\Http\Repositories\LevelRepository;

class LevelService
{
    protected LevelRepository $levelRepository;


    public function __construct(LevelRepository $levelRepository)
    {
        $this->levelRepository = $levelRepository;
    }

    public function index()
    {
        return $this->levelRepository->index();
    }

    public function store(array $data)
    {
        return $this->levelRepository->store($data);
    }

    public function update(array $data, int $ID)
    {
        return $this->levelRepository->update($data, $ID);
    }

    public function show(int $id)
    {
        return $this->levelRepository->show($id);
    }
}
